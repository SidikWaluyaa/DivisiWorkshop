<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;
use Carbon\Carbon;

echo "INVESTIGASI RESOLUSI 4 APRIL 2026\n";
echo "===============================\n";

$issues = CxIssue::where('status', 'RESOLVED')
    ->whereDate('resolved_at', '2026-04-04')
    ->with(['workOrder.workOrderServices'])
    ->get();

foreach ($issues as $i) {
    // Audit Jasa Baru (yg dibuat SETELAH issue dibuat)
    $servicesAfterIssue = $i->workOrder->workOrderServices->where('created_at', '>=', $i->created_at);
    
    // Filter non-OTO (seperti di Dashboard)
    $manualUpsellServices = $servicesAfterIssue->filter(function($s) {
        return empty($s->custom_service_name) || !str_starts_with($s->custom_service_name, 'OTO:');
    });

    echo "ID: {$i->id} | SPK: {$i->spk_number} | Type: " . ($i->resolution_type ?: 'NULL') . "\n";
    echo "   Created: " . $i->created_at->format('Y-m-d H:i') . " | Resolved: " . $i->resolved_at->format('Y-m-d H:i') . "\n";
    echo "   Total Jasa Baru (Manual): " . $manualUpsellServices->count() . "\n";
    
    if ($manualUpsellServices->count() > 0) {
        foreach ($manualUpsellServices as $s) {
            echo "   -> [Service ID: {$s->id}] {$s->category_name}/{$s->custom_service_name} | Created: " . $s->created_at->format('Y-m-d H:i') . " | Cost: {$s->cost}\n";
        }
    }
    echo "-------------------------------\n";
}
