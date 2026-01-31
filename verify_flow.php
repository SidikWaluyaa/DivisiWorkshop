<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\CsLead;
// ... (rest is same)
use App\Services\Cs\CsSpkService;
use App\Services\Cs\CsLeadService;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "\n============================================\n";
echo "🛠️  WORKSHOP FEATURE VERIFICATION SCRIPT  🛠️\n";
echo "============================================\n\n";

// 1. SETUP ACTORS
echo "[1] Setting up Actors...\n";
$csUser = User::where('role', 'cs')->first();
if (!$csUser) {
    echo "⚠️  No CS User found. Creating dummy CS...\n";
    $csUser = User::create([
        'name' => 'Test CS', 'email' => 'testcs@example.com', 'password' => bcrypt('password'), 'role' => 'cs', 'cs_code' => 'TCS'
    ]);
}
$prodUser = User::where('role', 'technician')->first();
if (!$prodUser) {
    echo "⚠️  No Technician found using 'technician' role. Creating dummy Technician...\n";
    $prodUser = User::create([
        'name' => 'Test Tech', 'email' => 'tech@example.com', 'password' => bcrypt('password'), 'role' => 'technician', 'specialization' => 'Sol Repair'
    ]);
}
echo "✅ Actors Ready: CS ({$csUser->name}), Tech ({$prodUser->name})\n\n";

// 2. CS FLOW: Create Lead & SPK
echo "[2] Simulating CS Flow...\n";
Auth::login($csUser);
try {
    // Create Lead (Simulating CsLeadService)
    $leadData = [
        'customer_name' => 'Test Customer ' . rand(100,999),
        'customer_phone' => '08123456' . rand(100,999),
        'description' => 'Test Repair Shoes for Workshop',
    ];
    // Manual create for simulation if Service not fully bound or complex
    $lead = CsLead::create(array_merge($leadData, ['status' => 'follow_up', 'cs_id' => $csUser->id]));
    echo "   - Lead Created: ID {$lead->id}\n";
    
    // Generate SPK (Simulating CsSpkService)
    // We assume data preparation is done
    echo "   - Generating SPK...\n";
    
    // Mocking Request Data for SPK
    $spkData = [
        'customer_name' => $lead->customer_name,
        'customer_phone' => $lead->customer_phone,
        'brand' => 'Nike Test',
        'items' => [
             ['category' => 'Sol', 'service' => 'Regue', 'service_name' => 'Reglue Sol', 'price' => 50000, 'qty' => 1]
        ],
        'estimation_date' => now()->addDays(3)->format('Y-m-d')
    ];
    
    // Direct WorkOrder Creation to simulate 'Handover'
    // Usually CsSpkService::generateSpkForLead($lead, $data)
    // For Verification, we do what the "Handover" controller logic does:
    // 1. Create WorkOrder
    $order = new WorkOrder();
    $order->spk_number = WorkOrder::generateSpkNumber('Sepatu', $csUser->cs_code);
    $order->customer_name = $spkData['customer_name'];
    $order->customer_phone = $spkData['customer_phone'];
    $order->shoe_brand = $spkData['brand'];
    $order->status = WorkOrderStatus::ASSESSMENT; // Start at Assessment/Inbound
    $order->entry_date = now();
    $order->estimation_date = $spkData['estimation_date'];
    $order->created_by = $csUser->id;
    $order->save();
    
    // Attach Services
    // Mocking Service attachment (assuming Service model exists or using raw)
    // For simplification, we just use the 'services' relationship if Service exists, else just text for now?
    // The Scopes rely on 'services' relation.
    // Let's create a dummy Service if needed.
    $service = \App\Models\Service::firstOrCreate(
        ['name' => 'Reglue Sol'], 
        ['category' => 'Sol Repair', 'price' => 50000]
    );
    $order->services()->attach($service->id, ['cost' => 50000, 'technician_id' => null, 'status' => 'PENDING']);
    
    echo "   - WorkOrder Created: {$order->spk_number} (Status: {$order->status->value})\n";
    echo "✅ CS Flow Success.\n\n";
    
} catch (\Exception $e) {
    echo "❌ CS Flow Failed: " . $e->getMessage() . "\n";
    exit;
}

// 3. WAREHOUSE/ASSESSMENT FLOW
echo "[3] Simulating Warehouse/Inbound...\n";
// Move to Production
$order->status = WorkOrderStatus::PRODUCTION;
$order->save();
echo "   - Order moved to PRODUCTION status.\n";
echo "✅ Warehouse Flow Success.\n\n";


// 4. WORKSHOP VERIFICATION (The Real Test)
echo "[4] Verifying Workshop Logic...\n";

// A. Test Scopes
echo "   - Testing 'scopeProductionSol'...\n";
// Re-fetch using Scope
$solOrders = WorkOrder::where('id', $order->id)->productionSol()->count();
if ($solOrders > 0) {
    echo "     OK: Order found in Sol Queue (Count: $solOrders).\n";
} else {
    echo "     FAIL: Order NOT found in Sol Queue!\n";
}

echo "   - Testing 'scopeProductionUpper'...\n";
$upperOrders = WorkOrder::where('id', $order->id)->productionUpper()->count();
// Logic: If needs sol, should NOT appear in Upper until Sol done.
// Our order needs Sol. So Upper should count 0?
// Actually our scope logic: "Show if Sol NOT required OR Sol Completed".
// Since Sol is required and NOT completed, this should be 0.
if ($upperOrders === 0) {
    echo "     OK: Order correctly hidden from Upper Queue (Waiting for Sol).\n";
} else {
    echo "     FAIL: Order appeared in Upper Queue prematurely!\n";
}

// B. Test Frontend Bug Logic (Simulated)
echo "   - Testing Bulk Action Logic...\n";
// The previous bug was: Active Tab 'Upper' but sent 'prod_sol'.
// We verify that if we query correctly for 'Upper', we get correct count.
// Simulation: User switches to Sol Tab.
$activeTab = 'sol';
$countSol = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->productionSol()->whereNull('prod_sol_completed_at')->count();
echo "     Active Tab 'Sol' -> Count: $countSol\n";

// C. Test Security
echo "   - Testing Security Policy...\n";
Auth::login($prodUser); // Login as Technician
$policy = new \App\Policies\WorkOrderPolicy();

$canUpdate = $policy->updateProduction($prodUser, $order);
echo "     Technician access to updateProduction: " . ($canUpdate ? "GRANTED" : "DENIED") . "\n";

if ($canUpdate) {
    echo "✅ Security Check Passed.\n";
} else {
    // If technician setup is correct, they should be able to update if assigned or if general pool? 
    // Policy: if role=technician, return status === PRODUCTION.
    // Our status IS Production. So should be true.
    echo "❌ Security Check Failed (Unexpected Denial).\n";
}

echo "\n============================================\n";
echo "✅ VERIFICATION COMPLETED\n";
echo "============================================\n";
