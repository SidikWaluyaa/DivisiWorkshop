<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\CxIssue;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;

$spk = 'S-2602-12-0004-SW';
$issue = CxIssue::where('spk_number', $spk)->first();

if (!$issue) {
    die("Issue for SPK $spk NOT FOUND! Check if the SPK number is correct and has a CX Issue.\n");
}

echo "--- DEEP DIVE: $spk ---\n";
echo "Issue Status: {$issue->status}\n";
echo "Issue Created: {$issue->created_at}\n";
echo "Issue Resolved: " . ($issue->resolved_at ?: 'PENDING') . "\n";
echo "Resolution Notes: |{$issue->resolution_notes}|\n\n";

$wo = $issue->workOrder;
if (!$wo) {
    die("WorkOrder record for $spk NOT FOUND in relationship!\n");
}

foreach ($wo->workOrderServices as $s) {
    echo "Service: [{$s->custom_service_name} / {$s->category_name}]\n";
    echo "Cost: {$s->cost} | Created: {$s->created_at}\n";
    
    $notes = strtolower($issue->resolution_notes ?? '');
    $cat = strtolower($s->category_name ?? '');
    $name = strtolower($s->custom_service_name ?? '');
    
    $isAfter = $s->created_at >= $issue->created_at;
    $matchCat = $cat && strpos($notes, $cat) !== false;
    $matchName = $name && strpos($notes, $name) !== false;
    
    echo "  > [Check]\n";
    echo "    - Created AFTER Issue ({$issue->created_at}): ".($isAfter?'YES':'NO')."\n";
    echo "    - Keyword Match in Notes: ".($matchCat || $matchName ?'YES':'NO')."\n";
    echo "---------------------------------\n";
}
