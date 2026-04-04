<?php

include 'vendor/autoload.php';
$app = include_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\CxIssue;
use App\Models\WorkOrderService;
use Carbon\Carbon;

$start = '2026-02-01 00:00:00';
$end = '2026-02-28 23:59:59';

echo "--- FEBRUARY 2026 DATA ---" . PHP_EOL;
echo "Issues created: " . CxIssue::whereBetween('created_at', [$start, $end])->count() . PHP_EOL;
echo "Issues resolved: " . CxIssue::whereBetween('resolved_at', [$start, $end])->count() . PHP_EOL;
echo "Services created: " . WorkOrderService::whereBetween('created_at', [$start, $end])->count() . PHP_EOL;

$sampleIssue = CxIssue::whereBetween('created_at', [$start, $end])->first();
if ($sampleIssue) {
    echo "Sample Issue: ID {$sampleIssue->id} | Created: {$sampleIssue->created_at} | Status: {$sampleIssue->status}" . PHP_EOL;
} else {
    echo "NO ISSUES CREATED IN FEB." . PHP_EOL;
}

$sampleService = WorkOrderService::whereBetween('created_at', [$start, $end])->first();
if ($sampleService) {
    echo "Sample Service: ID {$sampleService->id} | Created: {$sampleService->created_at}" . PHP_EOL;
}
