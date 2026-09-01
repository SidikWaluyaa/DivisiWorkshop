<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;

echo "1. Querying SPKs with scopeProductionReview()..." . PHP_EOL;
$readyForQc = WorkOrder::productionReview()
    ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
        $q->where('jenis_serah_terima', 'produksi_to_post_qc');
    })->get();

echo "Ready for QC candidates count: {$readyForQc->count()}" . PHP_EOL;

foreach ($readyForQc as $wo) {
    echo "- SPK #{$wo->spk_number} ({$wo->customer_name}) | Sol Done: " . ($wo->prod_sol_completed_at ? 'YES' : 'N/A') . " | Upper Done: " . ($wo->prod_upper_completed_at ? 'YES' : 'N/A') . " | Cleaning Done: " . ($wo->prod_cleaning_completed_at ? 'YES' : 'N/A') . PHP_EOL;
}

echo "Done!" . PHP_EOL;
