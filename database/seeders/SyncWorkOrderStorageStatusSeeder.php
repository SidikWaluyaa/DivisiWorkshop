<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkOrder;
use App\Models\StorageAssignment;

class SyncWorkOrderStorageStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active storage assignments
        $activeAssignments = StorageAssignment::where('status', 'stored')->get();
        $count = 0;

        foreach ($activeAssignments as $assignment) {
            $workOrder = WorkOrder::find($assignment->work_order_id);
            
            if ($workOrder) {
                // Check if WorkOrder needs update
                if ($workOrder->storage_rack_code !== $assignment->rack_code) {
                    // Update raw to bypass validation/events if needed, or use model update
                    // We use forceUpdate or DB update to ensure it sticks
                    $workOrder->update([
                        'storage_rack_code' => $assignment->rack_code,
                        'stored_at' => $assignment->stored_at,
                    ]);
                    $count++;
                    $this->command->info("Synced WO #{$workOrder->spk_number} to Rack {$assignment->rack_code}");
                }
            }
        }

        $this->command->info("Sync complete. Updated {$count} WorkOrders.");
    }
}
