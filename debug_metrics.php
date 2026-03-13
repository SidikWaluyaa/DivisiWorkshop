<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

$start = now()->startOfMonth();
$end = now();

echo "Range: " . $start->toDateString() . " to " . $end->toDateString() . "\n";

$completions = WorkOrder::where('status', WorkOrderStatus::SELESAI)
    ->whereDate('finished_date', '>=', $start)
    ->whereDate('finished_date', '<=', $end)
    ->count();

$entries = WorkOrder::whereDate('entry_date', '>=', $start)
    ->whereDate('entry_date', '<=', $end)
    ->count();

echo "Completions (Status Selesai + Range): " . $completions . "\n";
echo "Entries (Range): " . $entries . "\n";

// Check without status filter
$totalFinishedInRange = WorkOrder::whereDate('finished_date', '>=', $start)
    ->whereDate('finished_date', '<=', $end)
    ->count();

echo "Total finished_date in range (any status): " . $totalFinishedInRange . "\n";

// Check sample status
$sample = WorkOrder::whereNotNull('finished_date')->latest('finished_date')->first();
if ($sample) {
    echo "Sample finished_date: " . $sample->finished_date . "\n";
    echo "Sample status: " . $sample->status->value . "\n";
} else {
    echo "No work order with finished_date found.\n";
}
