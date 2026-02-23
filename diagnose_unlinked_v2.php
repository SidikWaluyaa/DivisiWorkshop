<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

// Find unlinked work orders
$unlinked = WorkOrder::whereNotExists(function ($query) {
    $query->select(DB::raw(1))
        ->from('customers')
        ->whereRaw('customers.phone = work_orders.customer_phone');
})->get();

echo "Total Unlinked Records: " . $unlinked->count() . "\n";

$noPhone = 0;
$mismatchedPhone = 0;
$sampleNoPhoneNames = [];

foreach($unlinked as $wo) {
    if (empty($wo->customer_phone)) {
        $noPhone++;
        if (count($sampleNoPhoneNames) < 10) {
            $sampleNoPhoneNames[] = "SPK: {$wo->spk_number} | Name: '{$wo->customer_name}'";
        }
    } else {
        $mismatchedPhone++;
    }
}

echo "Records with NULL/Empty Phone: $noPhone\n";
echo "Records with Mismatched Phone (format issue?): $mismatchedPhone\n";

if ($noPhone > 0) {
    echo "\nSamples of records with no phone:\n";
    foreach($sampleNoPhoneNames as $sample) {
        echo "- $sample\n";
    }
}

// Check if any of these names exist in customers table (case-insensitive or with spaces)
if ($noPhone > 0) {
    echo "\nChecking for fuzzy name matches for records with no phone...\n";
    $uniqueNames = $unlinked->where('customer_phone', '')->pluck('customer_name')->unique();
    if ($uniqueNames->isEmpty()) $uniqueNames = $unlinked->whereNull('customer_phone')->pluck('customer_name')->unique();
    
    $matches = 0;
    foreach($uniqueNames as $name) {
        if (!$name) continue;
        $cleanName = trim($name);
        $customer = Customer::where('name', 'LIKE', '%' . $cleanName . '%')->first();
        if ($customer) {
            $matches++;
            if ($matches <= 5) {
                echo "Found potential match: '$name' -> Customer: '{$customer->name}' (Phone: {$customer->phone})\n";
            }
        }
    }
    echo "Total fuzzy name matches found: $matches\n";
}
