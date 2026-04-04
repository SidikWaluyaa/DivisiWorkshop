<?php

include 'vendor/autoload.php';
$app = include_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\CxIssue;
use App\Models\WorkOrderService;
use Carbon\Carbon;

$start = Carbon::parse('2026-03-01')->startOfDay();
$end = Carbon::parse('2026-03-31')->endOfDay();

echo "--- FINANCIAL WIDGET DATA ---" . PHP_EOL;
$tjs = WorkOrderService::whereHas('workOrder.cxIssues', function($q) use ($start, $end) {
    $q->where('status', 'RESOLVED')->whereBetween('resolved_at', [$start, $end]);
})->get();

echo "Total Services linked to March-Resolved Issues: " . $tjs->count() . PHP_EOL;
echo "Total Unique SPK: " . $tjs->unique('work_order_id')->count() . PHP_EOL;
foreach ($tjs as $s) {
    echo "  Service: {$s->id} | Created: {$s->created_at} | Cost: {$s->cost} | SPK: {$s->work_order_id}" . PHP_EOL;
}

echo PHP_EOL . "--- RESOLVED CARD BREAKDOWN DATA ---" . PHP_EOL;
$resolvedIssuesQuery = CxIssue::where('status', 'RESOLVED')
    ->whereBetween('resolved_at', [$start, $end]);

$resolvedWithUpsell = (clone $resolvedIssuesQuery)
    ->whereHas('workOrder.workOrderServices', function($q) use ($start, $end) {
        // Query as written in CxDashboardController
        $q->whereBetween('work_order_services.created_at', [$start, $end]);
    })
    ->get();

echo "Issues counted as 'With Upsell': " . $resolvedWithUpsell->count() . PHP_EOL;
foreach ($resolvedWithUpsell as $i) {
    echo "  Issue ID: {$i->id} | Resolved: {$i->resolved_at} | SPK: {$i->work_order_id}" . PHP_EOL;
}
