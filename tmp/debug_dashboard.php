<?php
use App\Models\CxIssue;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use Carbon\Carbon;

$start = Carbon::now()->startOfDay();
$end = Carbon::now()->endOfDay();

echo "DEBUG CX DASHBOARD - " . date('Y-m-d H:i:s') . "\n";
echo "--------------------------------------------------\n";

// 1. Find all Resolved Issues today
$issues = CxIssue::where('status', 'RESOLVED')
    ->whereBetween('resolved_at', [$start, $end])
    ->get();

echo "Total Resolved Issues: " . $issues->count() . "\n";

foreach ($issues as $issue) {
    $wo = $issue->workOrder;
    if (!$wo) continue;
    
    echo "\nSPK: " . $wo->spk_number . " | Issue ID: " . $issue->id . " | Created: " . $issue->created_at . " | Resolved: " . $issue->resolved_at . "\n";
    
    $services = $wo->workOrderServices;
    echo "Total Services in SPK: " . $services->count() . "\n";
    
    foreach ($services as $s) {
        $name = $s->custom_service_name ?? $s->category_name;
        $hasNotes = !is_null($s->notes);
        $isAfterIssue = $s->created_at > $issue->created_at;
        
        echo "  - Service: " . $name . " (ID: ". $s->id .")\n";
        echo "    * Cost: " . $s->cost . "\n";
        echo "    * Created At: " . $s->created_at . "\n";
        echo "    * Has Notes: " . ($hasNotes ? 'YES (' . $s->notes . ')' : 'NO (NULL)') . "\n";
        echo "    * Is After CX Issue: " . ($isAfterIssue ? 'YES' : 'NO') . "\n";
        
        $reason = [];
        if (!$hasNotes) $reason[] = "MISSING NOTES";
        if (!$isAfterIssue) $reason[] = "TIMING (Created <= Issue)";
        
        if (!empty($reason)) {
            echo "    [FILTERED OUT BECAUSE: " . implode(', ', $reason) . "]\n";
        } else {
            echo "    [PASSED - SHOULD COUNT AS UPSELL]\n";
        }
    }
}
