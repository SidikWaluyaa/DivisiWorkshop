<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
$o = WorkOrder::where('customer_name', 'LIKE', '%Hidayat%')->first();
if (!$o) {
    // Try CsSpk just in case
    $o = \App\Models\CsSpk::where('customer_name', 'LIKE', '%Hidayat%')->first();
    echo "Model: CsSpk" . PHP_EOL;
} else {
    echo "Model: WorkOrder" . PHP_EOL;
}
if($o) {
    echo "SPK: " . $o->spk_number . PHP_EOL;
    echo "Status: " . (is_object($o->status) ? $o->status->value : $o->status) . PHP_EOL;
    echo "Is Revising: " . ($o->is_revising ? 'Yes' : 'No') . PHP_EOL;
    echo "QC Jahit: " . ($o->qc_jahit_completed_at ?: 'NULL') . PHP_EOL;
    echo "QC Cleanup: " . ($o->qc_cleanup_completed_at ?: 'NULL') . PHP_EOL;
    echo "QC Final: " . ($o->qc_final_completed_at ?: 'NULL') . PHP_EOL;
    echo "Previous Status: " . (is_object($o->previous_status) ? $o->previous_status->value : $o->previous_status) . PHP_EOL;
} else {
    echo "Order not found" . PHP_EOL;
}
