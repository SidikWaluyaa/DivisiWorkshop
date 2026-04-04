<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$start = Carbon::now()->startOfDay();
$end = Carbon::now()->endOfDay();

echo "--- GLOBAL SEARCH FOR DATA ADDED TODAY ({$start->toDateString()}) ---\n\n";

// 1. WorkOrderServices
echo "[1] WorkOrderService (Manual Extra Services):\n";
$wos = DB::table('work_order_services')
    ->join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
    ->whereBetween('work_order_services.created_at', [$start, $end])
    ->get(['work_orders.spk_number', 'work_order_services.custom_service_name', 'work_order_services.cost', 'work_order_services.created_at']);

if ($wos->isEmpty()) {
    echo "    None found.\n";
} else {
    foreach ($wos as $item) {
        echo "    - SPK: {$item->spk_number} | Svc: {$item->custom_service_name} | Cost: {$item->cost} | Created: {$item->created_at}\n";
    }
}

// 2. OTOS
echo "\n[2] OTO (Package Proposals):\n";
$otos = DB::table('otos')
    ->join('work_orders', 'otos.work_order_id', '=', 'work_orders.id')
    ->whereBetween('otos.created_at', [$start, $end])
    ->get(['work_orders.spk_number', 'otos.total_oto_price', 'otos.status', 'otos.created_at']);

if ($otos->isEmpty()) {
    echo "    None found.\n";
} else {
    foreach ($otos as $item) {
        echo "    - SPK: {$item->spk_number} | Price: {$item->total_oto_price} | Status: {$item->status} | Created: {$item->created_at}\n";
    }
}

// 3. CxIssues updated today
echo "\n[3] CxIssues Resolved/Updated Today:\n";
$issues = DB::table('cx_issues')
    ->whereBetween('updated_at', [$start, $end])
    ->get(['spk_number', 'status', 'resolution_notes', 'updated_at']);

if ($issues->isEmpty()) {
    echo "    None found.\n";
} else {
    foreach ($issues as $item) {
        echo "    - SPK: {$item->spk_number} | Status: {$item->status} | Notes: |{$item->resolution_notes}| \n";
    }
}
