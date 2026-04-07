<?php

use App\Models\CxIssue;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Script to Cleanup Duplicate CX Entries
 * Run via: php artisan tinker tmp/cleanup_duplicate_cx.php
 */

function cleanupDuplicates($execution = false) {
    echo "=== CX Duplicate Data Audit ===\n";
    
    // 1. Find Duplicate Resolutions
    // Pattern: Same WorkOrder, Same Day, Multiple RESOLVED status
    $duplicates = CxIssue::where('status', 'RESOLVED')
        ->select('work_order_id', DB::raw('DATE_FORMAT(resolved_at, "%Y-%m-%d %H:%i") as minutes'), DB::raw('COUNT(*) as count'))
        ->groupBy('work_order_id', 'minutes')
        ->having('count', '>', 1)
        ->get();

    if ($duplicates->isEmpty()) {
        echo "No clear duplicates found in history.\n";
        return;
    }

    echo "Found " . $duplicates->count() . " suspect SPKs with duplicate resolutions.\n";

    foreach ($duplicates as $dup) {
        $wo = WorkOrder::find($dup->work_order_id);
        $spk = $wo ? $wo->spk_number : "Unknown (#$dup->work_order_id)";
        echo "\nChecking SPK: $spk (Time: $dup->minutes, Count: $dup->count)\n";

        $issues = CxIssue::where('work_order_id', $dup->work_order_id)
            ->where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [
                Carbon::parse($dup->minutes)->startOfMinute(),
                Carbon::parse($dup->minutes)->endOfMinute()
            ])
            ->orderBy('id', 'asc')
            ->get();

        $keep = $issues->first();
        $toDelete = $issues->slice(1);

        echo "  - Keeping Issue ID: {$keep->id} (Type: {$keep->resolution_type})\n";

        foreach ($toDelete as $item) {
            echo "  - DELETING Duplicate Issue ID: {$item->id} (Type: {$item->resolution_type})\n";
            
            if ($execution) {
                $item->delete();
                echo "    [DONE] Deleted.\n";
            } else {
                echo "    [DRY RUN] Will delete.\n";
            }

            // If it's TAMBAH JASA, check services
            if ($item->resolution_type === 'tambah_jasa') {
                echo "    Searching for duplicate services in WorkOrderService...\n";
                
                // Find services created at same time
                $services = WorkOrderService::where('work_order_id', $dup->work_order_id)
                    ->whereBetween('created_at', [
                        Carbon::parse($item->resolved_at)->subSeconds(2),
                        Carbon::parse($item->resolved_at)->addSeconds(2)
                    ])
                    ->get();
                
                if ($services->count() > 0) {
                    // Group by service attributes to find actual duplicates
                    $groupedServices = $services->groupBy(function($s) {
                        return ($s->service_id ?? 'custom') . '|' . $s->custom_service_name . '|' . $s->cost;
                    });

                    foreach ($groupedServices as $key => $group) {
                        if ($group->count() > 1) {
                            // We have duplicates! Keep half, delete half (assuming double submission)
                            // Usually if it's 2 entries, we delete 1. If 4 (quadruple?), we delete 2.
                            $cleanupCount = floor($group->count() / 2);
                            echo "      Duplicate Service Type [$key] detected. Group Size: {$group->count()}, Cleanup: $cleanupCount\n";
                            
                            $itemsToKill = $group->slice(0, $cleanupCount);
                            foreach ($itemsToKill as $kill) {
                                echo "      - Killing Service ID: {$kill->id} (Cost: {$kill->cost})\n";
                                if ($execution) {
                                    $kill->delete();
                                    echo "        [DONE] Deleted.\n";
                                } else {
                                    echo "        [DRY RUN] Will delete.\n";
                                }
                            }
                        }
                    }
                }
            }
        }

        // Always recalculate at the end for the SPK
        if ($execution) {
            echo "  - Recalculating Total Price for $spk...\n";
            $wo->recalculateTotalPrice();
            echo "    [DONE] Final Price: " . number_format($wo->total_transaksi) . "\n";
        }
    }
}

// EXECUTION
// Set true to actually delete, false for dry run
$isExecution = true; 
cleanupDuplicates($isExecution);

echo "\nCleanup Process Completed.\n";
