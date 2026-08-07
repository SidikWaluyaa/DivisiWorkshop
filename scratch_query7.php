<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrderService;

$services = WorkOrderService::where('service_details->is_cx_additional', true)
    ->with('workOrder')
    ->get();

echo "Total CX Additional Services: " . $services->count() . "\n";
if ($services->count() > 0) {
    echo "Sample data:\n";
    $svc = $services->first();
    echo "ID: " . $svc->id . "\n";
    echo "Service Details: " . json_encode($svc->service_details) . "\n";
}
