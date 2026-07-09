<?php

require 'c:/laragon/www/SistemWorkshop/vendor/autoload.php';
$app = require_once 'c:/laragon/www/SistemWorkshop/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\Service;
use App\Models\CsLead;
use App\Models\CsSpk;
use App\Models\CsSpkItem;
use App\Services\Cs\CsSpkService;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;

echo "Seeding Fast Track test data...\n";

// 1. Ensure Services Allow Fast Track
$fastClean = Service::where('name', 'Fast Clean')->first();
if (!$fastClean) {
    $fastClean = Service::create([
        'name' => 'Fast Clean',
        'category' => 'Cleaning',
        'price' => 35000,
        'duration_minutes' => 30,
        'hk_days' => 1,
        'allow_fast_track' => 'yes'
    ]);
} else {
    $fastClean->update(['allow_fast_track' => 'yes']);
}

$deepClean = Service::where('name', 'Deep Clean')->first();
if (!$deepClean) {
    $deepClean = Service::create([
        'name' => 'Deep Clean',
        'category' => 'Cleaning',
        'price' => 75000,
        'duration_minutes' => 60,
        'hk_days' => 3,
        'allow_fast_track' => 'no'
    ]);
} else {
    $deepClean->update(['allow_fast_track' => 'no']);
}

$reglueSol = Service::where('name', 'Reglue Sol')->first();
if (!$reglueSol) {
    $reglueSol = Service::create([
        'name' => 'Reglue Sol',
        'category' => 'Reparasi Sol',
        'price' => 50000,
        'duration_minutes' => 120,
        'hk_days' => 4,
        'allow_fast_track' => 'no'
    ]);
} else {
    $reglueSol->update(['allow_fast_track' => 'no']);
}

// 2. Ensure CS Lead & Customer
$customer = \App\Models\Customer::firstOrCreate(
    ['phone' => '089999999999'],
    [
        'name' => 'Budi FastTrack',
        'email' => 'budi.fast@gmail.com',
        'address' => 'Gg. Cepat No. 7, Jakarta'
    ]
);

$lead = CsLead::firstOrCreate(
    ['customer_phone' => '089999999999'],
    [
        'customer_name' => 'Budi FastTrack',
        'customer_email' => 'budi.fast@gmail.com',
        'customer_address' => 'Gg. Cepat No. 7, Jakarta',
        'status' => 'CONVERTED',
        'cs_id' => 1, // Admin Gudang
        'channel' => 'ONLINE',
        'source' => 'WhatsApp'
    ]
);

$spkService = app(CsSpkService::class);

/**
 * Helper to create CS SPK and hand it to workshop
 */
function createCsSpkAndHandToWorkshop($lead, $customer, $servicesList, $expectedDeliveryDate = null, $priority = 'Normal') {
    $spkService = app(CsSpkService::class);
    
    // Create CsSpk
    $spk = CsSpk::create([
        'cs_lead_id' => $lead->id,
        'customer_id' => $customer->id,
        'total_price' => collect($servicesList)->sum('price'),
        'dp_amount' => 0,
        'dp_status' => CsSpk::DP_PENDING,
        'priority' => $priority,
        'delivery_type' => 'Offline',
        'cs_code' => 'SW',
        'status' => CsSpk::STATUS_DP_PAID,
        'expected_delivery_date' => $expectedDeliveryDate ?: now()->addDays(3),
    ]);

    // Create CsSpkItem
    $spkItem = CsSpkItem::create([
        'spk_id' => $spk->id,
        'category' => 'Sepatu',
        'shoe_brand' => 'Nike',
        'shoe_type' => 'Pegasus',
        'shoe_color' => 'Black-Orange',
        'shoe_size' => '42',
        'services' => $servicesList,
        'item_total_price' => collect($servicesList)->sum('price'),
        'original_price' => collect($servicesList)->sum('price'),
        'discount_amount' => 0,
        'status' => CsSpkItem::STATUS_PENDING,
        'hk_days' => 3,
    ]);

    // Format hand to workshop items input
    $itemsInput = [
        $spkItem->id => [
            'spk_item_id' => $spkItem->id,
            'item_type' => 'Sepatu',
            'shoe_brand' => 'Nike',
            'shoe_type' => 'Pegasus',
            'shoe_color' => 'Black-Orange',
            'shoe_size' => '42',
            'ref_photos' => []
        ]
    ];

    // Handover to Workshop
    $wos = $spkService->handToWorkshop($spk, $itemsInput, 1);
    return $wos[0];
}

// ==========================================
// SCENARIO 1: Fast Track SPK (Exactly 1 Fast Track Service)
// ==========================================
$fastCleanService = [
    [
        'id' => $fastClean->id,
        'name' => $fastClean->name,
        'category' => $fastClean->category,
        'price' => $fastClean->price
    ]
];
$wo1 = createCsSpkAndHandToWorkshop($lead, $customer, $fastCleanService);
echo "Scenario 1 (Fast Track SPK) created: {$wo1->spk_number} | fast_track_status: {$wo1->fast_track_status}\n";


// ==========================================
// SCENARIO 2: Normal SPK (1 Ineligible Service)
// ==========================================
$deepCleanService = [
    [
        'id' => $deepClean->id,
        'name' => $deepClean->name,
        'category' => $deepClean->category,
        'price' => $deepClean->price
    ]
];
$wo2 = createCsSpkAndHandToWorkshop($lead, $customer, $deepCleanService);
echo "Scenario 2 (Normal SPK - Deep Clean) created: {$wo2->spk_number} | fast_track_status: {$wo2->fast_track_status}\n";


// ==========================================
// SCENARIO 3: Normal SPK (2 Services: Fast Clean + Reglue Sol)
// ==========================================
$twoServices = [
    [
        'id' => $fastClean->id,
        'name' => $fastClean->name,
        'category' => $fastClean->category,
        'price' => $fastClean->price
    ],
    [
        'id' => $reglueSol->id,
        'name' => $reglueSol->name,
        'category' => $reglueSol->category,
        'price' => $reglueSol->price
    ]
];
$wo3 = createCsSpkAndHandToWorkshop($lead, $customer, $twoServices);
echo "Scenario 3 (Normal SPK - Multiple Services) created: {$wo3->spk_number} | fast_track_status: {$wo3->fast_track_status}\n";


// ==========================================
// SCENARIO 4: Fast Track SPK in SORTIR (3+ Days Overdue Warning)
// ==========================================
$wo4 = createCsSpkAndHandToWorkshop($lead, $customer, $fastCleanService);
$wo4->update([
    'status' => WorkOrderStatus::SORTIR,
    'created_at' => now()->subDays(4),
    'entry_date' => now()->subDays(4),
]);
echo "Scenario 4 (Fast Track SORTIR Overdue) created: {$wo4->spk_number} | fast_track_status: {$wo4->fast_track_status} | created_at: {$wo4->created_at->toDateString()}\n";


// ==========================================
// SCENARIO 5: Fast Track SPK in PRODUCTION (4+ Days Overdue Warning)
// ==========================================
$wo5 = createCsSpkAndHandToWorkshop($lead, $customer, $fastCleanService);
$wo5->update([
    'status' => WorkOrderStatus::PRODUCTION,
    'created_at' => now()->subDays(6),
    'entry_date' => now()->subDays(6),
    'prod_sol_started_at' => now()->subDays(5),
]);
echo "Scenario 5 (Fast Track PRODUCTION Overdue) created: {$wo5->spk_number} | fast_track_status: {$wo5->fast_track_status} | prod_sol_started_at: {$wo5->prod_sol_started_at->toDateString()}\n";

echo "All Fast Track scenarios seeded successfully!\n";
