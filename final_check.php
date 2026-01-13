<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WorkOrder;

echo "=== FINAL CHECK ===\n\n";

// Check if orders exist
$count = WorkOrder::count();
echo "Total Orders: {$count}\n\n";

if ($count > 0) {
    // Get first order
    $order = WorkOrder::with('services')->first();
    echo "Sample Order:\n";
    echo "  SPK: {$order->spk_number}\n";
    echo "  Customer: {$order->customer_name}\n";
    echo "  Phone: {$order->customer_phone}\n";
    echo "  Services: " . $order->services->count() . "\n\n";
    
    echo "Try tracking with:\n";
    echo "  SPK: {$order->spk_number}\n";
    echo "  Phone: {$order->customer_phone}\n\n";
    
    echo "URL: http://127.0.0.1:8000/track\n";
} else {
    echo "❌ NO DATA! Run: php artisan db:seed\n";
}
