<?php
$order = App\Models\WorkOrder::where('spk_number', 'SPK-000000-21')->first();
if (!$order) {
    echo "Order not found\n";
    exit;
}
echo "Order found: " . $order->id . "\n";
echo "Services count: " . $order->services->count() . "\n";
foreach ($order->services as $s) {
    echo " - Service: " . $s->name . " | Category: " . $s->category . "\n";
}
echo "Needs Sol: " . ($order->needs_sol ? 'YES' : 'NO') . "\n";
