<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = \DB::table('work_order_logs')
    ->where('action', 'status_change')
    ->where('step', 'CX_FOLLOWUP')
    ->orderBy('id', 'desc')
    ->take(5)
    ->get();
echo "\nLogs entering CX_FOLLOWUP:\n";
print_r($logs);
