<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;

class AuditCxFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recovery:audit-cx-flow {--fix : Automatically restore the wrongly routed SPKs to their correct status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit and optionally fix SPKs that were wrongly routed due to the old CX status transition bugs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fix = $this->option('fix');

        if ($fix) {
            $this->warn("=========================================");
            $this->warn("    PRODUCTION RUN: CORRECTING STATUS    ");
            $this->warn("=========================================");
        } else {
            $this->info("=========================================");
            $this->info("      AUDIT MODE: READ-ONLY TESTRUN      ");
            $this->info("=========================================");
        }

        // NOTE: Case A (Gudang SPKs in Production) is skipped because those orders are already actively being worked on in production and must not be demoted back to Assessment.

        // =========================================================================
        // CASE B: Workshop SPKs wrongly pushed back to Assessment instead of Workshop
        // =========================================================================
        $this->info("\nChecking Case B: Workshop SPKs wrongly sent back to Assessment...");

        $workshopOrders = WorkOrder::where('status', WorkOrderStatus::ASSESSMENT->value)
        ->whereHas('cxIssues', function($q) {
            $q->where('source', 'like', 'WORKSHOP_%');
        })
        ->whereHas('logs', function($q) {
            $q->whereIn('step', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC']);
        })
        ->get();

        if ($workshopOrders->isEmpty()) {
            $this->info("-> Clean: No wrongly routed Workshop SPKs found.");
        } else {
            $this->warn("-> Found " . $workshopOrders->count() . " wrongly routed Workshop SPKs:");
            
            $headers = ['ID', 'SPK Number', 'Customer', 'Current Status', 'Last Stasiun Kerja', 'Action Required'];
            $rows = [];
            
            foreach ($workshopOrders as $order) {
                // Find latest stasiun kerja from logs
                $lastWorkshopLog = $order->logs()
                    ->whereIn('step', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'])
                    ->orderBy('created_at', 'desc')
                    ->first();
                $targetStatus = $lastWorkshopLog ? $lastWorkshopLog->step : 'PRODUCTION';

                $rows[] = [
                    $order->id,
                    $order->spk_number,
                    $order->customer_name,
                    $order->status->value,
                    $targetStatus,
                    "Restore to " . $targetStatus
                ];
            }
            
            $this->table($headers, $rows);

            if ($fix) {
                $this->info("\nRestoring Case B SPKs to their previous stasiun kerja...");
                foreach ($workshopOrders as $order) {
                    $lastWorkshopLog = $order->logs()
                        ->whereIn('step', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'])
                        ->orderBy('created_at', 'desc')
                        ->first();
                    $targetStatus = $lastWorkshopLog ? $lastWorkshopLog->step : 'PRODUCTION';

                    DB::transaction(function () use ($order, $targetStatus) {
                        $order->update([
                            'status' => $targetStatus,
                            'previous_status' => WorkOrderStatus::CX_FOLLOWUP->value
                        ]);
                        
                        $order->logs()->create([
                            'step' => $targetStatus,
                            'action' => 'CX_BUGFIX_RESTORE',
                            'user_id' => 1, // System/Admin
                            'description' => "[SYSTEM] Mengembalikan SPK Workshop ke stasiun $targetStatus (Koreksi salah rute CX)"
                        ]);
                    });
                    $this->line("  - <info>SPK {$order->spk_number}</info> successfully restored to {$targetStatus}.");
                }
            }
        }

        $this->info("\n=========================================");
        if ($fix) {
            $this->info("   Correction run complete. Database is sync'd! ");
        } else {
            $this->info("   Audit complete. Run with --fix to apply changes. ");
        }
        $this->info("=========================================");

        return 0;
    }
}
