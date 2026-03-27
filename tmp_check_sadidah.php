<?php
$customer = \App\Models\Customer::withTrashed()->where('name', 'like', '%Sadidah%')->first();
echo "CUSTOMER:\n";
print_r($customer ? $customer->toArray() : 'NULL');

$orders = \App\Models\WorkOrder::where('customer_name', 'like', '%Sadidah%')->get();
echo "\nORDERS:\n";
foreach($orders as $o) {
   echo "SPK: {$o->spk_number} | Phone: {$o->customer_phone} | Name: {$o->customer_name}\n";
}
