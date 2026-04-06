<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;

echo "TEST KEYWORD-MATCHING JUNIAR (0941-IK)\n";
echo "=====================================\n";

$i = CxIssue::find(494);
if (!$i) {
    echo "ID 494 NOT FOUND!\n";
    return;
}

echo "CATATAN JAWABAN: " . ($i->resolution_notes ?: '(Kosong)') . "\n";
echo "-------------------------------------\n";

foreach ($i->workOrder->workOrderServices as $s) {
    if ($s->created_at < $i->created_at) continue; // Skip original services
    
    $catName = $s->category_name;
    $customName = $s->custom_service_name;
    
    $foundCat = !empty($catName) && str_contains(strtolower($i->resolution_notes), strtolower($catName));
    $foundCustom = !empty($customName) && str_contains(strtolower($i->resolution_notes), strtolower($customName));
    
    // Loosened keyword matching (words within strings)
    $isActuallyUpsell = false;
    if ($foundCat || $foundCustom) {
        $isActuallyUpsell = true;
    } else {
        // Broaden search to words
        $words = explode(' ', strtolower($customName ?: $catName));
        foreach ($words as $word) {
            if (strlen($word) > 3 && str_contains(strtolower($i->resolution_notes), $word)) {
                $isActuallyUpsell = true;
                break;
            }
        }
    }

    echo "- SERVICE: " . ($customName ?: $catName) . " (Rp " . number_format($s->cost) . ")\n";
    echo "  Status: " . ($isActuallyUpsell ? "✅ COCOK (IT'S AN UPSELL)" : "❌ DIBUANG (NOT IN NOTES)") . "\n";
}
