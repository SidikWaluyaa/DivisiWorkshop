<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\WorkOrderService;

$spk = 'S-2603-29-0909-AW';

echo "\n--- INVESTIGASI SPK: $spk ---\n";
$wo = WorkOrder::where('spk_number', $spk)->first();

if (!$wo) {
    echo "SPK Tidak Ditemukan.\n";
    die();
}

$issues = $wo->cxIssues;
echo "\nCX ISSUES FOUND: " . $issues->count() . "\n";
foreach ($issues as $i) {
    echo "ID: {$i->id} | Status: {$i->status} | Created: {$i->created_at} | Resolved: {$i->resolved_at}\n";
    echo "Notes: \"{$i->resolution_notes}\"\n";
}

$services = $wo->workOrderServices;
echo "\nWORK ORDER SERVICES FOUND: " . $services->count() . "\n";
foreach ($services as $s) {
    $name = $s->custom_service_name ?: ($s->service->name ?? 'Unknown');
    echo "ID: {$s->id} | Name: $name | Category: {$s->category_name} | Cost: {$s->cost}\n";
    echo "Created: {$s->created_at}\n";
    echo "Details: " . json_encode($s->service_details) . "\n";
}

echo "\n--- EVALUASI LOGIKA ---\n";
foreach ($issues as $i) {
    foreach ($services as $s) {
        $name = strtolower($s->custom_service_name ?: ($s->service->name ?? ''));
        $cat = strtolower($s->category_name);
        $resNotes = strtolower($i->resolution_notes);
        
        $timeMatch = $s->created_at >= $i->created_at;
        $keywordMatch = str_contains($resNotes, 'tambah jasa') || ($name && str_contains($resNotes, $name)) || ($cat && str_contains($resNotes, $cat));
        $detailsNotEmpty = json_encode($s->service_details) !== '[]';
        
        echo "Issue {$i->id} vs Service {$s->id}:\n";
        echo " - Time Match (Service >= Issue): " . ($timeMatch ? "YES" : "NO") . "\n";
        echo " - Keyword Match in Notes: " . ($keywordMatch ? "YES" : "NO") . "\n";
        echo " - Details Not Empty: " . ($detailsNotEmpty ? "YES" : "NO") . "\n";
    }
}
