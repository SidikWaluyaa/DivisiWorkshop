<?php

use App\Models\CxIssue;
use App\Models\WorkOrder;

$spk = 'S-2603-30-0978-AW';
echo "Checking SPK: $spk\n";

$wo = WorkOrder::where('spk_number', $spk)->first();
if (!$wo) {
    die("WorkOrder not found.\n");
}

$issues = CxIssue::where('work_order_id', $wo->id)->orderBy('resolved_at', 'desc')->get();
echo "Count: " . $issues->count() . "\n";

foreach ($issues as $issue) {
    echo "ID: {$issue->id} | Status: {$issue->status} | Resolved At: {$issue->resolved_at} | Type: {$issue->resolution_type} | Message: {$issue->resolution_notes}\n";
}
