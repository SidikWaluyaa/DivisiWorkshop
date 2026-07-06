<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\StorageAssignment;
use App\Models\WorkOrder;
use App\Models\StorageRack;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $activeProductionStatuses = [
            'PREPARATION',
            'SORTIR',
            'PRODUCTION',
            'QC',
            'REVISI'
        ];

        $completedStatuses = [
            'SELESAI',
            'HISTORY',
            'BATAL',
            'DONASI',
            'DIANTAR'
        ];

        DB::transaction(function () use ($activeProductionStatuses, $completedStatuses) {
            $now = now();
            $racksToRecalculate = [];

            // 1. Process active production status orders in 'before' rack
            $activeOrphans = StorageAssignment::where('status', 'stored')
                ->where('category', 'before')
                ->whereHas('workOrder', function ($query) use ($activeProductionStatuses) {
                    $query->whereIn('status', $activeProductionStatuses);
                })
                ->get();

            foreach ($activeOrphans as $assignment) {
                // Update assignment status to retrieved
                $assignment->update([
                    'status' => 'retrieved',
                    'retrieved_at' => $now,
                    'notes' => $assignment->notes . "\nSystem Migration Cleanup: Auto-released since work order is actively running in production."
                ]);

                // Update work order: remove rack but DO NOT fill retrieved_at or taken_date
                $workOrder = $assignment->workOrder;
                if ($workOrder && $workOrder->storage_rack_code === $assignment->rack_code) {
                    $workOrder->update([
                        'storage_rack_code' => null,
                        'stored_at' => null,
                    ]);
                }

                if ($assignment->rack_code) {
                    $racksToRecalculate[$assignment->rack_code] = true;
                }
            }

            // 2. Process completed/cancelled status orders in 'before' rack
            $completedOrphans = StorageAssignment::where('status', 'stored')
                ->where('category', 'before')
                ->whereHas('workOrder', function ($query) use ($completedStatuses) {
                    $query->whereIn('status', $completedStatuses);
                })
                ->get();

            foreach ($completedOrphans as $assignment) {
                // Update assignment status to retrieved
                $assignment->update([
                    'status' => 'retrieved',
                    'retrieved_at' => $now,
                    'notes' => $assignment->notes . "\nSystem Migration Cleanup: Auto-released since work order is completed or cancelled."
                ]);

                // Update work order: remove rack and update retrieved_at & taken_date
                $workOrder = $assignment->workOrder;
                if ($workOrder && $workOrder->storage_rack_code === $assignment->rack_code) {
                    $workOrder->update([
                        'storage_rack_code' => null,
                        'stored_at' => null,
                        'retrieved_at' => $now,
                        'taken_date' => $workOrder->taken_date ?? $now,
                    ]);
                }

                if ($assignment->rack_code) {
                    $racksToRecalculate[$assignment->rack_code] = true;
                }
            }

            // 3. Recalculate counts for all modified racks
            foreach (array_keys($racksToRecalculate) as $rackCode) {
                $racks = StorageRack::where('rack_code', $rackCode)->get();
                foreach ($racks as $rack) {
                    $actualCount = StorageAssignment::where('rack_code', $rackCode)
                        ->where('category', $rack->category)
                        ->where('status', 'stored')
                        ->count();
                    $rack->update(['current_count' => $actualCount]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data cleanup cannot be reversed cleanly
    }
};
