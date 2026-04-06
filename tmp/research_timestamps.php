<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrderService;
use Carbon\Carbon;

$start = Carbon::parse('2026-04-04')->startOfDay();
$end = Carbon::parse('2026-04-04')->endOfDay();

$services = WorkOrderService::join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
    ->join('cx_issues', 'work_orders.id', '=', 'cx_issues.work_order_id')
    ->where('cx_issues.status', 'RESOLVED')
    ->whereBetween('cx_issues.resolved_at', [$start, $end])
    ->select(
        'work_orders.spk_number',
        'work_order_services.custom_service_name',
        'work_order_services.category_name',
        'work_order_services.cost',
        'work_order_services.created_at as service_created',
        'cx_issues.resolved_at as issue_resolved',
        'cx_issues.resolution_notes'
    )
    ->get();

echo "RISET SELISIH WAKTU (TIMESTAMPDIFF)\n";
echo "========================================================================\n";
echo str_pad("SPK", 18) . " | " . str_pad("Jasa", 25) . " | " . str_pad("Delta (Sec)", 12) . " | Cost\n";
echo str_repeat("-", 80) . "\n";

foreach ($services as $s) {
    $sTime = Carbon::parse($s->service_created);
    $iTime = Carbon::parse($s->issue_resolved);
    $delta = $sTime->diffInSeconds($iTime, false);
    
    $name = $s->custom_service_name ?: $s->category_name;
    echo str_pad($s->spk_number, 18) . " | " . str_pad(substr($name, 0, 25), 25) . " | " . str_pad($delta, 12) . " | Rp" . number_format($s->cost) . "\n";
    if (abs($delta) > 300) {
        echo "   [!] Selisih > 5 menit (Kemungkinan Jasa Bengkel)\n";
    }
}
echo "========================================================================\n";
