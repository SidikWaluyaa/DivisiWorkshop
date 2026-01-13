<?php

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new WorkflowService();

// Mock an order
$order = new WorkOrder();
$order->status = 'PRODUCTION';
// Simulate incomplete production
// We need to mock 'services' relation or just set timestamps manually?
// getIsProductionFinishedAttribute relies on relation.
// Creating a real order in DB is safer.

echo "Creating Test Order...\n";
$order = WorkOrder::create([
    'spk_number' => 'TEST-' . time(),
    'customer_name' => 'Tester',
    'status' => 'PRODUCTION', 
    'entry_date' => now()
]);

// Add dummy service (Sol)
DB::table('services')->insertOrIgnore([
    'id' => 999, 'name' => 'Test Sol', 'category' => 'Sol Repair', 'price' => 10000
]);
DB::table('work_order_services')->insert([
    'work_order_id' => $order->id, 'service_id' => 999, 'cost' => 10000
]);

echo "Attempting to move Incomplete Order to QC...\n";
try {
    $service->updateStatus($order, WorkOrderStatus::QC);
    echo "ERROR: Should have failed!\n";
} catch (Exception $e) {
    echo "SUCCESS: Caught expected error: " . $e->getMessage() . "\n";
}

// Now complete it
echo "Completing Production...\n";
$order->prod_sol_completed_at = now();
$order->save();

// Re-fetch to clear cache? Accessor usually strictly dynamic.
$order = WorkOrder::find($order->id);

echo "Attempting to move Completed Order to QC...\n";
try {
    $service->updateStatus($order, WorkOrderStatus::QC);
    echo "SUCCESS: Moved to QC!\n";
} catch (Exception $e) {
    echo "ERROR: Failed to move: " . $e->getMessage() . "\n";
}

// Cleanup
$order->delete();
