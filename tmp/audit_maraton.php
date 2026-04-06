<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;
use Carbon\Carbon;

echo "AUDIT MARATON: 1 - 3 APRIL 2026\n";
echo "================================\n";

for ($d = 1; $d <= 3; $d++) {
    $date = "2026-04-0" . $d;
    echo "\n--- TANGGAL: $date ---\n";
    
    $issues = CxIssue::whereDate('resolved_at', $date)
        ->where('status', 'RESOLVED')
        ->get();
        
    if ($issues->isEmpty()) {
        echo "TIDAK ADA DATA RESOLVED.\n";
        continue;
    }

    foreach ($issues as $i) {
        $wo = $i->workOrder;
        $spk = $wo ? $wo->spk_number : 'N/A';
        echo "ID: {$i->id} | SPK: {$spk} | Type: {$i->resolution_type}\n";
        echo "NOTES: '{$i->resolution_notes}'\n";
        echo "TECH NOTES: '" . ($wo ? $wo->technician_notes : '') . "'\n";
        echo "JASA SETELAH TIKET:\n";
        
        if ($wo) {
            foreach ($wo->workOrderServices as $s) {
                if ($s->created_at >= $i->created_at) {
                    $name = $s->custom_service_name ?: $s->category_name;
                    echo "  - {$name} (Rp " . number_format($s->cost) . ")\n";
                }
            }
        }
        echo "--------------------------\n";
    }
}
