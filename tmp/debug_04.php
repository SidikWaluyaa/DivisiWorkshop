<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;
use App\Models\WorkOrder;
use App\Services\CxDashboardService;
use Carbon\Carbon;

echo "🕵️‍♂️ DEBUG AKURASI DASHBOARD (04 APRIL)\n";
echo "=======================================\n";

$service = new CxDashboardService();
$start = Carbon::parse('2026-04-04')->startOfDay();
$end = Carbon::parse('2026-04-04')->endOfDay();

// Ambil isu 0909-AW
$issue = CxIssue::whereHas('workOrder', function($q) {
    $q->where('spk_number', 'LIKE', '%0909-AW');
})->whereBetween('resolved_at', [$start, $end])->first();

if (!$issue) {
    echo "❌ Isu 0909-AW tidak ditemukan untuk tanggal tersebut.\n";
    exit;
}

$wo = $issue->workOrder;
echo "SPK: " . $wo->spk_number . "\n";
echo "Notes Resolusi: [" . $issue->resolution_notes . "]\n";
echo "Notes Teknisi: [" . $wo->technician_notes . "]\n\n";

echo "CEK JASA SATU PER SATU:\n";
foreach ($wo->workOrderServices as $svc) {
    $name = $svc->custom_service_name ?: $svc->category_name;
    
    // Gunakan Reflection untuk akses method private isServiceMatchIssue
    $method = new ReflectionMethod(CxDashboardService::class, 'isServiceMatchIssue');
    $method->setAccessible(true);
    $isMatch = $method->invoke($service, $svc, $issue);
    
    echo sprintf("- [%s] | Harga: %d | Status Match: %s\n", 
        $name, 
        $svc->cost, 
        $isMatch ? "✅ MATCH (IKUT DIHITUNG)" : "❌ NO MATCH"
    );
}

echo "\n---------------------------------------\n";
$summary = $service->getSummary($start, $end, true);
echo "TOTAL NOMINAL DI DASHBOARD: Rp " . number_format($summary['upsell']['total_nominal']) . "\n";
