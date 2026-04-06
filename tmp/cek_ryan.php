<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\CxIssue;

echo "INVESTIGASI RYAN (0394-AW):\n";
$wo = WorkOrder::where('spk_number', 'LIKE', '%0394-AW%')->first();
if ($wo) {
    $issue = CxIssue::where('work_order_id', $wo->id)->first();
    if ($issue) {
        echo "Tiket Created: {$issue->created_at}\n";
    }
    echo "Daftar Jasa:\n";
    foreach ($wo->workOrderServices as $s) {
        $name = $s->custom_service_name ?: $s->category_name;
        echo "- {$name} (Rp " . number_format($s->cost) . ") | Created: {$s->created_at}\n";
    }
} else {
    echo "SPK NOT FOUND!\n";
}
