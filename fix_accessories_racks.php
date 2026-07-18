<?php
// Load Laravel Console Bootstrap
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\StorageRack;
use Illuminate\Support\Facades\DB;

echo "--- START CLEANING WORK ORDERS STORAGE RACK MISMATCHES ---\n";

DB::transaction(function() {
    // 1. Get all work orders where storage_rack_code is currently set
    $orders = WorkOrder::whereNotNull('storage_rack_code')->get();
    
    $affectedCount = 0;
    
    foreach ($orders as $order) {
        // Find the rack details
        $rack = StorageRack::where('rack_code', $order->storage_rack_code)->first();
        
        if ($rack && $rack->category->value === 'accessories') {
            echo "Mismatch Found - SPK: {$order->spk_number} | Rack Code: {$order->storage_rack_code} (Category: Accessories)\n";
            echo " -> Action: Resetting work_orders.storage_rack_code and stored_at to NULL.\n";
            
            $order->update([
                'storage_rack_code' => null,
                'stored_at' => null,
            ]);
            
            $affectedCount++;
        }
    }
    
    echo "--- CLEANUP COMPLETED ---\n";
    echo "Total affected work orders fixed: {$affectedCount}\n";
});
