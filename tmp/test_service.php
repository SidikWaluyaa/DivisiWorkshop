<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Services\CxDashboardService;
use Carbon\Carbon;

$start = Carbon::now()->startOfDay();
$end = Carbon::now()->endOfDay();

echo "--- DASHBOARD SERVICE TEST (Today: {$start->toDateString()}) ---\n\n";

$service = new CxDashboardService();
$summary = $service->getSummary($start, $end, true); // Force refresh

$kpi = $summary['kpi'];
$upsell = $summary['upsell'];

echo "[1] KPI Summary (Top Widgets):\n";
echo "    - Total Issues: {$kpi['total']}\n";
echo "    - Resolved: {$kpi['resolved']}\n";
echo "    - Resolved With Upsell: {$kpi['resolved_with_upsell']}\n";
echo "    - Resolved No Upsell: {$kpi['resolved_no_upsell']}\n";

echo "\n[2] Upsell Metrics (Financial Widgets):\n";
echo "    - Total Nominal: {$upsell['total_nominal']}\n";
echo "    - Total SPK Volume: {$upsell['total_volume']}\n";
echo "    - OTO Nominal: {$upsell['oto_nominal']}\n";
echo "    - OTO Volume: {$upsell['oto_volume']}\n";

if ($kpi['resolved_with_upsell'] == 0 && $upsell['total_nominal'] == 0) {
    echo "\n[!] ALERT: Values are ZERO. Something is blocking the detection.\n";
} else {
    echo "\n[+] SUCCESS: Data detected in service.\n";
}
