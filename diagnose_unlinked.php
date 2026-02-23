<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\Customer;

$unlinked = WorkOrder::where(function($q) {
    $q->whereNull('customer_phone')->orWhere('customer_phone', '');
})->get();

echo "Total Unlinked (Empty Phone): " . $unlinked->count() . "\n";

$matchesFound = 0;
foreach($unlinked as $wo) {
    if ($wo->customer_name) {
        $customer = Customer::where('name', $wo->customer_name)->first();
        if ($customer) {
            $matchesFound++;
            if ($matchesFound <= 5) {
                echo "Match: SPK {$wo->spk_number} | Name: {$wo->customer_name} -> Found Customer with Phone: {$customer->phone}\n";
            }
        }
    }
}

echo "Total Matches Found by Name: $matchesFound\n";
