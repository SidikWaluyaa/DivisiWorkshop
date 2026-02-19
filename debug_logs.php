<?php

use App\Models\WorkOrderLog;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orderId = 14;
$logs = WorkOrderLog::with('user')
    ->where('work_order_id', $orderId)
    ->orderBy('created_at', 'asc')
    ->get();

foreach ($logs as $log) {
    echo "ID: {$log->id} | User: " . ($log->user->name ?? 'N/A') . " | Action: {$log->action} | Desc: {$log->description}\n";
}
