<?php

namespace App\Observers;

use App\Models\WorkOrder;
use App\Models\Customer;

class WorkOrderObserver
{
    /**
     * Handle the WorkOrder "saved" event.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return void
     */
    public function saved(WorkOrder $workOrder)
    {
        $normalizedPhone = \App\Helpers\PhoneHelper::normalize($workOrder->customer_phone);

        if ($normalizedPhone) {
            // Check if customer already exists by normalized phone
            $customer = Customer::where('phone', $normalizedPhone)->first();

            if (!$customer) {
                // Create new customer record automatically
                Customer::create([
                    'name' => $workOrder->customer_name,
                    'phone' => $normalizedPhone,
                    'email' => $workOrder->customer_email,
                    'address' => $workOrder->customer_address,
                ]);
            } else {
                // Optional: Update existing customer data if it's missing
                $updates = [];
                if (!$customer->name && $workOrder->customer_name) $updates['name'] = $workOrder->customer_name;
                if (!$customer->email && $workOrder->customer_email) $updates['email'] = $workOrder->customer_email;
                if (!$customer->address && $workOrder->customer_address) $updates['address'] = $workOrder->customer_address;

                if (!empty($updates)) {
                    $customer->update($updates);
                }
            }
        } else if ($workOrder->customer_phone) {
            // Log warning if normalization fails instead of crashing
            \Illuminate\Support\Facades\Log::warning("Customer sync skipped for WO #{$workOrder->spk_number}: Phone '{$workOrder->customer_phone}' is invalid after normalization.");
        }
    }
}
