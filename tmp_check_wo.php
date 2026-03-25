<?php
$user = \App\Models\User::where('name', 'Vina')->first();
$wos = \App\Models\WorkOrder::where('created_by', $user->id)
    ->whereDate('entry_date', '2026-03-25')
    ->get();

echo "TOTAL WORK ORDER DARI VINA TANGGAL 25 MARET: " . $wos->count() . "\n";
foreach($wos as $idx => $wo) {
    echo ($idx+1) . ". SPK: " . $wo->spk_number 
        . " | CUSTOMER: " . $wo->customer_name 
        . " | BRAND: " . $wo->shoe_brand 
        . " | STATUS BENGKEL: " . $wo->status->value 
        . "\n";
}
