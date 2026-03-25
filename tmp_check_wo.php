<?php
$start = '2026-03-25 00:00:00';
$end = '2026-03-25 23:59:59';
$wos = \App\Models\WorkOrder::whereBetween('entry_date', [$start, $end])
    ->where('spk_number', 'LIKE', '%-VN')
    ->get();

echo "=== ALL WORK ORDERS ENDING WITH -VN ON MARCH 25 ===\n";
echo "Total Count: " . $wos->count() . "\n";
foreach($wos as $wo) {
    echo $wo->spk_number . " | " . $wo->status->value . " | Cust: " . $wo->customer_name . "\n";
}
