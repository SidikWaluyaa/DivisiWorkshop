<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkOrder;
use App\Models\StorageAssignment;
use App\Models\WorkOrderLog;

class SyncWorkOrderStorageStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Sync work order storage_rack_code for active assignments
        $activeAssignments = StorageAssignment::where('status', 'stored')->get();
        $syncCount = 0;

        foreach ($activeAssignments as $assignment) {
            $workOrder = WorkOrder::find($assignment->work_order_id);
            
            if ($workOrder) {
                if ($workOrder->storage_rack_code !== $assignment->rack_code) {
                    $workOrder->update([
                        'storage_rack_code' => $assignment->rack_code,
                        'stored_at' => $assignment->stored_at,
                    ]);
                    $syncCount++;
                    $this->command->info("Synced WO #{$workOrder->spk_number} to Rack {$assignment->rack_code}");
                }
            }
        }
        $this->command->info("Sync complete. Updated {$syncCount} WorkOrders.");

        // 2. Generate missing WorkOrderLog timeline events for ALL storage assignments
        $allAssignments = StorageAssignment::all();
        $logCount = 0;

        foreach ($allAssignments as $assignment) {
            $catLabel = $assignment->category === 'before' ? 'Inbound' : ($assignment->category === 'accessories' ? 'Aksesoris' : 'Finish');
            
            // Check/Create stored log
            $storedLogExists = WorkOrderLog::where('work_order_id', $assignment->work_order_id)
                ->where('action', 'rack_assigned')
                ->where('description', 'like', "%{$assignment->rack_code}%")
                ->exists();

            if (!$storedLogExists) {
                WorkOrderLog::create([
                    'work_order_id' => $assignment->work_order_id,
                    'user_id' => $assignment->stored_by ?? 1,
                    'step' => 'LOGISTICS',
                    'action' => 'rack_assigned',
                    'description' => "Barang disimpan di Rak {$catLabel} {$assignment->rack_code}.",
                    'created_at' => $assignment->stored_at,
                    'updated_at' => $assignment->stored_at,
                ]);
                $logCount++;
            }

            // Check/Create retrieved log if retrieved
            if ($assignment->status === 'retrieved' && $assignment->retrieved_at) {
                $retrievedLogExists = WorkOrderLog::where('work_order_id', $assignment->work_order_id)
                    ->where('action', 'rack_retrieved')
                    ->where('description', 'like', "%{$assignment->rack_code}%")
                    ->exists();

                if (!$retrievedLogExists) {
                    WorkOrderLog::create([
                        'work_order_id' => $assignment->work_order_id,
                        'user_id' => $assignment->retrieved_by ?? 1,
                        'step' => 'LOGISTICS',
                        'action' => 'rack_retrieved',
                        'description' => "Barang diambil dari Rak {$assignment->rack_code}.",
                        'created_at' => $assignment->retrieved_at,
                        'updated_at' => $assignment->retrieved_at,
                    ]);
                    $logCount++;
                }
            }
        }

        $this->command->info("Timeline logs sync complete. Generated {$logCount} timeline event logs.");
    }
}
