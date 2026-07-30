<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'];

echo "CX_FOLLOWUP -> STAGE:\n";
foreach ($stages as $stage) {
    $count = \DB::table('work_order_logs')
        ->where('step', $stage)
        ->where('action', 'STATUS_CHANGE')
        ->where('description', 'like', "Status berubah dari CX_FOLLOWUP ke %")
        ->distinct('work_order_id')
        ->count('work_order_id');
    echo "- CX_FOLLOWUP -> {$stage} : {$count} SPK\n";
}

echo "\nSTAGE -> CX_FOLLOWUP:\n";
foreach ($stages as $stage) {
    $count = \DB::table('work_order_logs')
        ->where('step', 'CX_FOLLOWUP')
        ->where('action', 'STATUS_CHANGE')
        ->where('description', 'like', "Status berubah dari {$stage} ke %")
        ->distinct('work_order_id')
        ->count('work_order_id');
    echo "- {$stage} -> CX_FOLLOWUP : {$count} SPK\n";
}
