<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\CxIssue;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$start = Carbon::now()->startOfDay();
$end = Carbon::now()->endOfDay();

echo "--- CX DASHBAORD DIAGNOSIS: ".date('Y-m-d')." ---\n\n";

// 1. KPI
$resolvedCount = CxIssue::where('status', 'RESOLVED')
    ->whereBetween('resolved_at', [$start, $end])
    ->count();
echo "Total Resolved Today: $resolvedCount\n\n";

// 2. OTO (Direct)
$otoSum = DB::table('otos')
    ->where('status', 'ACCEPTED')
    ->whereBetween('customer_responded_at', [$start, $end])
    ->sum('total_oto_price');
$otoCount = DB::table('otos')
    ->where('status', 'ACCEPTED')
    ->whereBetween('customer_responded_at', [$start, $end])
    ->count();
echo "OTO Summary (Direct):\n";
echo "- Volume: $otoCount | Nominal: $otoSum\n\n";

// 3. Tambah Jasa (Deep Dive)
echo "--- TAMBAH JASA DEEP DIVE ---\n";
$issues = CxIssue::where('status', 'RESOLVED')
    ->whereBetween('resolved_at', [$start, $end])
    ->get();

foreach ($issues as $issue) {
    echo "Issue SPK: {$issue->spk_number} | Resolved: {$issue->resolved_at}\n";
    echo "Notes: {$issue->resolution_notes}\n";
    
    $wo = WorkOrder::where('id', $issue->work_order_id)->first();
    if ($wo) {
        foreach ($wo->workOrderServices as $svc) {
            $cat = strtolower($svc->category_name ?? '');
            $name = strtolower($svc->custom_service_name ?? '');
            $notes = strtolower($issue->resolution_notes ?? '');
            
            // Check Match
            $isUpsell = ($svc->created_at >= $issue->created_at);
            $match = ($cat && str_contains($notes, $cat)) || ($name && str_contains($notes, $name));
            $isOto = $name && str_starts_with($name, 'oto:');
            
            echo "  - [{$svc->custom_service_name}] Cost: {$svc->cost} | Post-Issue: ".($isUpsell?'YES':'NO')." | Keyword Match: ".($match?'YES':'NO')." | OTO Prefix: ".($isOto?'YES':'NO')."\n";
        }
    }
    echo "--------------------------\n";
}
