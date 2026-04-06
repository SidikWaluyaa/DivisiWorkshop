<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;
use Carbon\Carbon;

$start = Carbon::parse('2026-04-01')->startOfDay();
$end = Carbon::parse('2026-04-01')->endOfDay();

$issues = CxIssue::where('status', 'RESOLVED')
    ->whereBetween('resolved_at', [$start, $end])
    ->with(['workOrder.workOrderServices'])
    ->get();

echo "AUDIT LENGKAP CX RESOLVED - 1 APRIL 2026\n";
echo "========================================================================\n";
echo "Total Tiket RESOLVED: " . $issues->count() . "\n\n";

foreach ($issues as $i) {
    echo "SPK: " . $i->spk_number . " | Resolved At: " . $i->resolved_at . "\n";
    echo "Notes: " . ($i->resolution_notes ?: "(Kosong)") . "\n";
    echo "Services:\n";
    foreach ($i->workOrder->workOrderServices as $s) {
        $name = $s->custom_service_name ?: $s->category_name;
        $isCreatedAfter = $s->created_at >= $i->created_at ? "YA" : "TIDAK";
        echo "   - [Created After Issue: $isCreatedAfter] {$name} (Rp" . number_format($s->cost) . ")\n";
        echo "     Created At: {$s->created_at} | Details: " . json_encode($s->service_details) . "\n";
    }
    echo "--------------------------\n";
}
