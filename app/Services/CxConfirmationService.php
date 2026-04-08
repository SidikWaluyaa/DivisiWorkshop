<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\CxAfterConfirmation;
use Illuminate\Support\Facades\Log;

class CxConfirmationService
{
    /**
     * Create a confirmation record when a shoe is picked up.
     */
    public function createFromOrder(WorkOrder $order)
    {
        try {
            // Check if record already exists to prevent duplicates
            $exists = CxAfterConfirmation::where('work_order_id', $order->id)->exists();
            if ($exists) {
                return;
            }

            CxAfterConfirmation::create([
                'work_order_id' => $order->id,
                'entered_at' => now(),
                // Initially empty, will be updated by CX team
            ]);

            Log::info("Created Konfirmasi After record for SPK: {$order->spk_number}");
        } catch (\Exception $e) {
            Log::error("Failed to create Konfirmasi After record: " . $e->getMessage());
        }
    }
}
