<?php

namespace App\Console\Commands;

use App\Models\WorkOrder;
use App\Models\StorageAssignment;
use App\Models\StorageRack;
use App\Enums\WorkOrderStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestoreInboundRacks extends Command
{
    protected $signature = 'storage:restore-inbound';
    protected $description = 'Safely restore active inbound rack assignments for work orders in pre-preparation statuses';

    public function handle()
    {
        $this->info('Starting recovery of inbound rack assignments in production...');

        $inboundStatuses = [
            WorkOrderStatus::DITERIMA,
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::WAITING_PAYMENT,
            WorkOrderStatus::READY_TO_DISPATCH,
            WorkOrderStatus::OTW_WORKSHOP,
            WorkOrderStatus::CX_FOLLOWUP,
        ];

        // Find work orders currently in inbound statuses
        $workOrders = WorkOrder::whereIn('status', $inboundStatuses)->get();

        $this->info("Found " . $workOrders->count() . " active work orders in inbound/pre-prep statuses.");
        $restoredCount = 0;

        foreach ($workOrders as $workOrder) {
            // Check if it already has an active stored assignment
            $hasActive = $workOrder->storageAssignments()
                ->where('status', 'stored')
                ->exists();

            if ($hasActive) {
                $this->line("- SPK: {$workOrder->spk_number} | Status: {$workOrder->status->value} | Already active in rack: {$workOrder->storage_rack_code}");
                continue;
            }

            // Look for its last retrieved assignment in a before/inbound rack
            $lastAssignment = $workOrder->storageAssignments()
                ->where('status', 'retrieved')
                ->where('category', 'before')
                ->latest('retrieved_at')
                ->first();

            if ($lastAssignment) {
                $this->line("<fg=yellow>- SPK: {$workOrder->spk_number} | Status: {$workOrder->status->value} | Restoring to rack: {$lastAssignment->rack_code} (Stored at: {$lastAssignment->stored_at})</>");
                
                DB::transaction(function () use ($workOrder, $lastAssignment) {
                    // Update Assignment back to stored
                    $lastAssignment->update([
                        'retrieved_at' => null,
                        'retrieved_by' => null,
                        'status' => 'stored',
                    ]);

                    // Update Work Order
                    $workOrder->update([
                        'storage_rack_code' => $lastAssignment->rack_code,
                        'stored_at' => $lastAssignment->stored_at,
                        'retrieved_at' => null,
                    ]);
                });

                $restoredCount++;
            } else {
                $this->line("- SPK: {$workOrder->spk_number} | Status: {$workOrder->status->value} | Never racked (Skipping)");
            }
        }

        if ($restoredCount > 0) {
            $this->info("Syncing rack capacity counts...");
            $this->call('storage:sync-counts');
            $this->info("✓ Successfully restored {$restoredCount} active inbound rack assignment(s)!");
        } else {
            $this->info("✓ No active inbound rack assignments needed recovery.");
        }

        return self::SUCCESS;
    }
}
