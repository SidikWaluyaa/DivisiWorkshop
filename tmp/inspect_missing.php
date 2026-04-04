<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;

$spks = ['S-2603-30-0932-RQ', 'S-2603-30-0941-IK'];

foreach ($spks as $spk) {
    echo "\n=== Inspecting Missing SPK: $spk ===\n";
    $wo = WorkOrder::where('spk_number', $spk)->first();
    if (!$wo) {
        echo "Not found.\n";
        continue;
    }

    foreach ($wo->workOrderServices as $s) {
        $name = $s->custom_service_name ?: ($s->service->name ?? 'Unknown');
        echo " - ID: {$s->id} | Name: $name\n";
        echo "   Details: " . json_encode($s->service_details) . "\n";
        echo "   Created At: {$s->created_at}\n";
    }
}
