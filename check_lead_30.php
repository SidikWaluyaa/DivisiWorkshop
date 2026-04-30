<?php
use App\Models\CsLead;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$lead = CsLead::with(['quotations.quotationItems', 'spk.items'])->find(30);

if (!$lead) {
    echo "Lead 30 not found\n";
    exit;
}

echo "Lead ID: " . $lead->id . "\n";
echo "Lead Status: " . $lead->status . "\n";

if ($lead->spk) {
    echo "SPK ID: " . $lead->spk->id . "\n";
    echo "SPK Number: " . $lead->spk->spk_number . "\n";
    echo "SPK Status: " . $lead->spk->status . "\n";
    echo "SPK Work Order ID: " . ($lead->spk->work_order_id ?: 'NULL') . "\n";
    foreach ($lead->spk->items as $item) {
        echo "Item #" . $item->item_number . " (" . $item->shoe_brand . " " . $item->shoe_type . ")\n";
        echo "Item Total Price: " . $item->item_total_price . "\n";
        echo "Services Data: " . json_encode($item->services) . "\n";
    }
} else {
    echo "No SPK found for Lead 30\n";
}
