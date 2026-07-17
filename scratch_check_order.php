<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$order = App\Models\WorkOrder::find(46);
if ($order) {
    echo "ID: " . $order->id . "\n";
    echo "SPK: " . $order->spk_number . "\n";
    echo "Status: " . ($order->status instanceof \BackedEnum ? $order->status->value : $order->status) . "\n";
} else {
    echo "Order 46 not found\n";
}
