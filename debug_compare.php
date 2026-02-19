<?php

use App\Models\WorkOrder;
use App\Models\WorkOrderLog;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orderId = 14;
$order = WorkOrder::with(['prepWashingBy', 'prepSolBy', 'prepUpperBy'])->findOrFail($orderId);

echo "Order #$orderId Assignments:\n";
echo "Prep Washing By: " . ($order->prepWashingBy->name ?? 'None') . " (" . ($order->prep_washing_by ?? 'N/A') . ")\n";
echo "Prep Sol By: " . ($order->prepSolBy->name ?? 'None') . " (" . ($order->prep_sol_by ?? 'N/A') . ")\n";
echo "Prep Upper By: " . ($order->prepUpperBy->name ?? 'None') . " (" . ($order->prep_upper_by ?? 'N/A') . ")\n";

echo "\nLogs for Order #$orderId:\n";
$logs = WorkOrderLog::with('user')
    ->where('work_order_id', $orderId)
    ->orderBy('created_at', 'asc')
    ->get();

foreach ($logs as $log) {
    echo "ID: {$log->id} | USER: " . ($log->user->name ?? 'N/A') . " ({$log->user_id}) | ACTION: {$log->action} | DESC: {$log->description}\n";
}
