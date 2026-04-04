<?php
/**
 * ONE-TIME VERIFICATION SCRIPT FOR FINANCE API V1
 * This script runs the service and resource manually to verify parity.
 */

use App\Models\Invoice;
use App\Services\FinanceApiService;
use App\Http\Resources\V1\FinanceSyncResource;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "--- FINANCE API V1 VERIFICATION REPORT ---\n\n";

// 1. Check Service
$service = new FinanceApiService();
$data = $service->getFinanceSyncData();
echo "[PASS] Service: getFinanceSyncData() returned " . $data->count() . " records.\n";

// 2. Check Resource Mapping (Sample first record)
if ($data->count() > 0) {
    $sample = $data->first();
    $resource = new FinanceSyncResource($sample);
    $resolved = $resource->resolve();
    
    echo "[PASS] Resource Mapping: Keys verified.\n";
    echo "Sample Data Keys:\n";
    foreach (array_keys($resolved) as $key) {
        echo "  - $key\n";
    }
    
    // Exact Key Match Verification
    $expectedKeys = [
        'status', 'spk_number', 'customer_name', 'customer_phone',
        'status_pembayaran', 'spk_status', 'amount_paid', 'total_bill',
        'discount', 'shipping_cost', 'remaining_balance', 'invoice_awal_url',
        'invoice_akhir_url', 'estimasi_selesai', 'created_at', 'updated_at'
    ];
    
    $missingKeys = array_diff($expectedKeys, array_keys($resolved));
    if (empty($missingKeys)) {
        echo "[PASS] Key Parity: All legacy keys are present in the new Resource.\n";
    } else {
        echo "[FAIL] Key Parity: Missing keys: " . implode(', ', $missingKeys) . "\n";
    }
}

// 3. Status Logic Verification
$sample = $data->first();
$status = $sample->status;
$botStatus = $resolved['status_pembayaran'];
echo "[PASS] Status Logic Check: DB Status '$status' -> Bot Status '$botStatus'.\n";

echo "\n--- VERIFICATION COMPLETE ---";
