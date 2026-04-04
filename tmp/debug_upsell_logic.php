<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Set range to Today for initial check
$start = Carbon::now()->startOfDay();
$end = Carbon::now()->endOfDay();

echo "=== Debug Opsell Logic (Today: " . $start->toDateString() . ") ===\n";

$query = WorkOrderService::join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
    ->join('cx_issues', 'work_orders.id', '=', 'cx_issues.work_order_id')
    ->where('cx_issues.status', 'RESOLVED')
    ->whereBetween('cx_issues.resolved_at', [$start, $end]);

$allPotential = (clone $query)->select(
    'work_orders.spk_number',
    'work_order_services.id as service_id',
    'work_order_services.category_name',
    'work_order_services.custom_service_name',
    'work_order_services.service_details',
    'cx_issues.resolution_notes',
    'work_order_services.created_at as service_created',
    'cx_issues.created_at as issue_created'
)->get();

echo "Found " . $allPotential->count() . " potential services linked to Resolved CX Issues today.\n";

foreach ($allPotential as $row) {
    echo "\n------------------------------------------------\n";
    echo "SPK: {$row->spk_number} | Service ID: {$row->service_id}\n";
    echo "Service: " . ($row->custom_service_name ?: $row->category_name) . "\n";
    echo "Created: {$row->service_created} | Issue Created: {$row->issue_created}\n";
    
    // Check Date Logic
    $dateMatch = $row->service_created >= $row->issue_created;
    echo "Time Check (Service >= Issue): " . ($dateMatch ? "PASSED" : "FAILED") . "\n";
    
    // Check Keywords Logic
    $resNotes = strtolower($row->resolution_notes);
    $keywordMatch = str_contains($resNotes, 'tambah jasa') || 
                   str_contains($resNotes, strtolower($row->category_name)) || 
                   ($row->custom_service_name && str_contains($resNotes, strtolower($row->custom_service_name)));
    echo "Keyword Match (notes vs name): " . ($keywordMatch ? "PASSED" : "FAILED") . " (Notes: \"{$row->resolution_notes}\")\n";
    
    // Check Instruction Logic (New Filter)
    $details = $row->service_details;
    $hasInstruction = false;
    $detailsStr = json_encode($details);
    if ($detailsStr && (str_contains($detailsStr, '"instruction"') || str_contains($detailsStr, 'instruction'))) {
        $hasInstruction = true;
    }
    echo "Instruction Note Check: " . ($hasInstruction ? "PASSED" : "FAILED") . " (Details: $detailsStr)\n";
    
    if ($dateMatch && $keywordMatch && $hasInstruction) {
        echo ">>> STATUS: INCLUDED in Dashboard Summary <<<\n";
    } else {
        echo ">>> STATUS: EXCLUDED from Dashboard Summary <<<\n";
    }
}
