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
            // Ambil data customer termasuk yang sudah dihapus secara lunak
            $customer = Customer::withTrashed()
                ->where('phone', $normalizedPhone)
                ->first();

            if (!$customer) {
                try {
                    // Pembuatan secara atomik
                    $customer = Customer::create([
                        'name' => $workOrder->customer_name,
                        'phone' => $normalizedPhone,
                        'email' => $workOrder->customer_email,
                        'address' => $workOrder->customer_address,
                    ]);
                } catch (\Exception $e) {
                    // Jika pembuatan gagal (karena race condition), ambil ulang datanya
                    $customer = Customer::withTrashed()->where('phone', $normalizedPhone)->first();
                }
            }

            if ($customer) {
                // Restore jika data sebelumnya terhapus (soft-deleted)
                if ($customer->trashed()) {
                    $customer->restore();
                }

                // Perbarui data yang kosong
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
