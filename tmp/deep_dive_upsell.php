<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrderService;
use Carbon\Carbon;

$start = Carbon::parse('2026-04-04')->startOfDay();
$end = Carbon::parse('2026-04-04')->endOfDay();

$potentialServices = WorkOrderService::join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
    ->join('cx_issues', 'work_orders.id', '=', 'cx_issues.work_order_id')
    ->where('cx_issues.status', 'RESOLVED')
    ->whereBetween('cx_issues.resolved_at', [$start, $end])
    ->whereRaw('work_order_services.created_at >= cx_issues.created_at')
    ->where('work_order_services.service_details', '!=', '[]')
    ->select('work_order_services.*', 'work_orders.spk_number', 'cx_issues.resolution_notes as issue_notes')
    ->get();

echo "INVESTIGASI DETAIL JASA (TOTAL POTENSIAL: " . $potentialServices->count() . ")\n";
echo "========================================================================\n";

foreach ($potentialServices as $s) {
    echo "SPK: {$s->spk_number} | Jasa: " . ($s->custom_service_name ?: $s->category_name) . " | Rp" . number_format($s->cost) . "\n";
    echo "Issue Notes: \"{$s->issue_notes}\"\n";
    echo "Service Details: " . json_encode($s->service_details) . "\n";
    echo "------------------------------------------------------------------------\n";
}
