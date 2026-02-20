<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateCustomerAddressTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-customer-address-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique address verification tokens and URLs for all customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customers = \App\Models\Customer::whereNull('address_token')
            ->orWhere('address_token', '')
            ->get();
            
        $count = 0;
        $appUrl = config('app.url');

        foreach ($customers as $customer) {
            $token = bin2hex(random_bytes(16));
            $customer->address_token = $token;
            $customer->address_verification_url = $appUrl . "/verifikasi-alamat/" . $token;
            
            if ($customer->save()) {
                $count++;
            }
        }

        $this->info("Successfully generated tokens for {$count} customers.");
    }
}
