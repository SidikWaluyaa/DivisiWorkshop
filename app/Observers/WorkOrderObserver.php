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
        if ($workOrder->customer_phone) {
            // Check if customer already exists by normalized phone
            $customer = Customer::where('phone', $workOrder->customer_phone)->first();

            if (!$customer) {
                // Create new customer record automatically
                Customer::create([
                    'name' => $workOrder->customer_name,
                    'phone' => $workOrder->customer_phone,
                    'email' => $workOrder->customer_email,
                    'address' => $workOrder->customer_address,
                    // If city/province/etc were in WorkOrder, we would sync them too
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
        }
    }
}
