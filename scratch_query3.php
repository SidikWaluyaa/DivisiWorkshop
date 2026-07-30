<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \DB::table('work_order_logs')
    ->where('step', 'SORTIR')
    ->where('action', 'STATUS_CHANGE')
    ->count();

echo "Total row in work_order_logs with step=SORTIR and action=STATUS_CHANGE: {$count}\n";

$countDistinct = \DB::table('work_order_logs')
    ->where('step', 'SORTIR')
    ->where('action', 'STATUS_CHANGE')
    ->distinct('work_order_id')
    ->count('work_order_id');

echo "Total unique SPKs in SORTIR: {$countDistinct}\n";
