<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;

$spk = 'S-2603-29-0909-AW';
$wo = WorkOrder::where('spk_number', $spk)->first();

if (!$wo) {
    die("SPK $spk tidak ditemukan.\n");
}

echo "\n=== DETAIL CX ISSUE ===\n";
foreach ($wo->cxIssues as $issue) {
    echo $issue->toJson(JSON_PRETTY_PRINT) . "\n";
}

echo "\n=== DETAIL SERVICES ===\n";
foreach ($wo->workOrderServices as $service) {
    echo "ID: {$service->id} | Name: " . ($service->custom_service_name ?: $service->category_name) . " | Cost: {$service->cost}\n";
    echo "Created: {$service->created_at}\n";
    echo "Details: " . json_encode($service->service_details) . "\n";
    echo "--------------------------\n";
}
