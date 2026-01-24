<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Models\Customer;
use App\Helpers\PhoneHelper;

class SyncCustomersFromWorkOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:sync-from-workorders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing WorkOrders to Customer database and normalize phone numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync process...');

        $workOrders = WorkOrder::whereNotNull('customer_phone')->get();
        $count = 0;
        $updatedWo = 0;

        foreach ($workOrders as $wo) {
            // 1. Normalize the phone number in the WorkOrder itself if not yet normalized
            $originalPhone = $wo->getRawOriginal('customer_phone');
            $normalizedPhone = PhoneHelper::normalize($originalPhone);

            if ($originalPhone !== $normalizedPhone) {
                $wo->customer_phone = $normalizedPhone;
                $wo->save(); // This will also trigger the observer, but we'll handle creation explicitly for safety
                $updatedWo++;
            }

            // 2. Ensure customer exists
            $customer = Customer::where('phone', $normalizedPhone)->first();

            if (!$customer) {
                Customer::create([
                    'name' => $wo->customer_name,
                    'phone' => $normalizedPhone,
                    'email' => $wo->customer_email,
                    'address' => $wo->customer_address,
                ]);
                $count++;
            }
        }

        $this->info("Sync complete!");
        $this->info("Normalized $updatedWo existing WorkOrder phone numbers.");
        $this->info("Created $count new customer records.");
    }
}
