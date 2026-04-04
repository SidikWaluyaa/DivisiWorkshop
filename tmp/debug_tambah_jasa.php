<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\CxIssue;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;

$spk = 'T-2602-21-0003-SW';
$issue = CxIssue::where('spk_number', $spk)->first();

if (!$issue) {
    die("Issue for SPK $spk NOT FOUND!\n");
}

echo "--- DEEP DIVE: $spk ---\n";
echo "Status: {$issue->status}\n";
echo "Created: {$issue->created_at}\n";
echo "Resolved: {$issue->resolved_at}\n";
echo "Notes: |{$issue->resolution_notes}|\n\n";

$wo = $issue->workOrder;
foreach ($wo->workOrderServices as $s) {
    echo "Service ID: {$s->id}\n";
    echo "Custom Name: |{$s->custom_service_name}|\n";
    echo "Category Name: |{$s->category_name}|\n";
    echo "Cost: {$s->cost}\n";
    echo "Created: {$s->created_at}\n";
    
    $notes = strtolower($issue->resolution_notes ?? '');
    $cat = strtolower($s->category_name ?? '');
    $name = strtolower($s->custom_service_name ?? '');
    
    $isAfter = $s->created_at >= $issue->created_at;
    $matchCat = $cat && strpos($notes, $cat) !== false;
    $matchName = $name && strpos($notes, $name) !== false;
    
    echo "  > [Condition Check]\n";
    echo "    - Created After Issue: ".($isAfter?'YES':'NO')."\n";
    echo "    - Match Category: ".($matchCat?'YES':'NO')." (Target: '$cat')\n";
    echo "    - Match Name: ".($matchName?'YES':'NO')." (Target: '$name')\n";
    echo "---------------------------------\n";
}
