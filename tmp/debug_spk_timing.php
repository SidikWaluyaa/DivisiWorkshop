<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use App\Models\CxIssue;

$spk = 'S-2602-12-0004-SW';
$wo = WorkOrder::where('spk_number', $spk)->first();

if (!$wo) {
    echo "WorkOrder not found\n";
    exit;
}

echo "WO: {$wo->id} | Created: {$wo->created_at}\n";
foreach ($wo->workOrderServices as $s) {
    echo "SERVICE: {$s->id} | Created: {$s->created_at} | Name: " . ($s->custom_service_name ?? $s->category_name) . " | Cost: {$s->cost}\n";
}
foreach ($wo->cxIssues as $i) {
    echo "ISSUE: {$i->id} | Created: {$i->created_at} | Status: {$i->status} | Resolved: {$i->resolved_at}\n";
}
