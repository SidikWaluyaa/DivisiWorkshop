<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrderService;
use App\Models\Service;

$brokenServices = WorkOrderService::whereNull('category_name')
    ->orWhere('category_name', '')
    ->get();

echo "Found " . $brokenServices->count() . " services with missing categories.\n";

foreach ($brokenServices as $ws) {
    if ($ws->service_id) {
        $service = Service::find($ws->service_id);
        if ($service) {
            echo "SPK #{$ws->work_order_id}: Fixing service '{$service->name}' with category '{$service->category}'\n";
            $ws->update([
                'category_name' => $service->category ?? 'Custom',
                'custom_service_name' => $ws->custom_service_name ?? $service->name
            ]);
        }
    } else {
        echo "SPK #{$ws->work_order_id}: Manual/Custom service found without category.\n";
    }
}

echo "Cleanup finished.\n";
