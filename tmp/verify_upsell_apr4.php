<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$start = Carbon::parse('2026-04-04')->startOfDay();
$end = Carbon::parse('2026-04-04')->endOfDay();

echo "\n============================================\n";
echo "VERIFIKASI DATA TAMBAH JASA - 4 APRIL 2026\n";
echo "============================================\n";

$tambahJasaQuery = WorkOrderService::join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
    ->join('cx_issues', 'work_orders.id', '=', 'cx_issues.work_order_id')
    ->where('cx_issues.status', 'RESOLVED')
    ->whereBetween('cx_issues.resolved_at', [$start, $end])
    ->whereRaw('work_order_services.created_at >= cx_issues.created_at')
    ->where(function($q) {
        $q->whereNull('work_order_services.custom_service_name')
          ->orWhere('work_order_services.custom_service_name', 'NOT LIKE', 'OTO:%');
    })
    ->where(function($q) {
        $q->where('work_order_services.service_details', '!=', '[]')
           ->where('work_order_services.service_details', '!=', 'null')
           ->where('work_order_services.service_details', 'NOT LIKE', '""');
    })
    ->whereRaw('LOWER(cx_issues.resolution_notes) NOT LIKE "%tanpa tambah jasa%"')
    ->whereRaw('LOWER(cx_issues.resolution_notes) NOT LIKE "%tidak ada tambah jasa%"')
    ->select('work_order_services.*', 'work_orders.spk_number', 'cx_issues.resolution_notes')
    ->orderBy('work_order_services.cost', 'desc')
    ->get();

$totalNominal = $tambahJasaQuery->sum('cost');
$totalVolume = $tambahJasaQuery->unique('work_order_id')->count();

echo "\nREKAPITULASI SUMMARY (NEW LOGIC):\n";
echo "Total Nominal : Rp" . number_format($totalNominal, 0, ',', '.') . "\n";
echo "Total Volume  : $totalVolume SPK\n";
echo "--------------------------------------------\n";

echo "\nRINCIAN ITEM (PER JASA):\n";
echo str_pad("SPK", 20) . " | " . str_pad("Jasa", 30) . " | " . "Nominal\n";
echo str_repeat("-", 65) . "\n";

foreach ($tambahJasaQuery as $item) {
    $serviceName = $item->custom_service_name ?: ($item->category_name ?: 'Unknown');
    echo str_pad($item->spk_number, 20) . " | " . str_pad(substr($serviceName, 0, 30), 30) . " | Rp" . number_format($item->cost, 0, ',', '.') . "\n";
}

echo "\n============================================\n";
echo "Selesai. Bandingkan angka di atas dengan Dashboard.\n";
echo "============================================\n";
