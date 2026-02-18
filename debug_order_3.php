<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orderId = 3;
$order = \App\Models\WorkOrder::with('photos')->find($orderId);

if ($order) {
    echo "Order ID: " . $order->id . "\n";
    echo "SPK: " . $order->spk_number . "\n";
    echo "Finish Report URL: " . ($order->finish_report_url ?? 'NULL') . "\n";
    echo "Total Photos: " . $order->photos->count() . "\n";
    
    foreach ($order->photos as $photo) {
        echo " - Photo ID: {$photo->id}, Step: {$photo->step}, Path: {$photo->file_path}\n";
    }
} else {
    echo "Order not found.\n";
}
