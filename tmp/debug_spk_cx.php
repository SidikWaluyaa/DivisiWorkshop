<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\WorkOrderService;

$spks = ['S-2603-11-0411-QA', 'S-2603-09-0331-QA'];

foreach ($spks as $spk) {
    echo "\n=== Investigating SPK: $spk ===\n";
    $wo = WorkOrder::where('spk_number', $spk)->first();
    
    if (!$wo) {
        echo "SPK Not Found.\n";
        continue;
    }

    echo "WorkOrder ID: {$wo->id} | Status: {$wo->status}\n";
    
    $issues = $wo->cxIssues;
    echo "Found " . $issues->count() . " CX Issues:\n";
    foreach ($issues as $i) {
        echo " - ID: {$i->id} | Status: {$i->status} | Res Notes: {$i->resolution_notes} | Resolved At: {$i->resolved_at}\n";
    }

    $services = $wo->workOrderServices;
    echo "\nFound " . $services->count() . " Services:\n";
    foreach ($services as $s) {
        $name = $s->custom_service_name ?: ($s->service->name ?? 'Unknown');
        $details = $s->service_details;
        echo " - ID: {$s->id} | Name: $name | Category: {$s->category_name} | Created: {$s->created_at}\n";
        echo "   Details: " . json_encode($details) . "\n";
    }
}
