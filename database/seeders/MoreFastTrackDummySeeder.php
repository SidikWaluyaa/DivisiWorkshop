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
use App\Models\Customer;
use App\Services\Cs\CsSpkService;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;

echo "Seeding additional dummy data for Prep, Prod, and QC...\n";

// Ensure Services
$fastClean = Service::where('name', 'Fast Clean')->first();
$deepClean = Service::where('name', 'Deep Clean')->first();
$reglueSol = Service::where('name', 'Reglue Sol')->first();

// Ensure Customer & CS Lead
$normalizedPhone = \App\Helpers\PhoneHelper::normalize('087777777777');
$customer = Customer::where('phone', $normalizedPhone)->first();
if (!$customer) {
    $customer = Customer::create([
        'phone' => '087777777777',
        'name' => 'John Doe',
        'email' => 'john.doe@gmail.com',
        'address' => 'Jl. Test Dummy No. 10'
    ]);
}

$lead = CsLead::where('customer_phone', '087777777777')
              ->orWhere('customer_phone', $normalizedPhone)
              ->first();
if (!$lead) {
    $lead = CsLead::create([
        'customer_phone' => '087777777777',
        'customer_name' => 'John Doe',
        'customer_email' => 'john.doe@gmail.com',
        'customer_address' => 'Jl. Test Dummy No. 10',
        'status' => 'CONVERTED',
        'cs_id' => 1,
        'channel' => 'ONLINE',
        'source' => 'WhatsApp'
    ]);
}

$spkService = app(CsSpkService::class);

function createTestOrder($lead, $customer, $servicesList, $priority, $status, $dateOffsetDays = 0) {
    $spkService = app(CsSpkService::class);
    
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
        'expected_delivery_date' => now()->addDays(3),
    ]);

    $spkItem = CsSpkItem::create([
        'spk_id' => $spk->id,
        'category' => 'Sepatu',
        'shoe_brand' => 'Nike',
        'shoe_type' => 'Pegasus',
        'shoe_color' => 'Black-White',
        'shoe_size' => '42',
        'services' => $servicesList,
        'item_total_price' => collect($servicesList)->sum('price'),
        'original_price' => collect($servicesList)->sum('price'),
        'discount_amount' => 0,
        'status' => CsSpkItem::STATUS_PENDING,
        'hk_days' => 3,
    ]);

    $itemsInput = [
        $spkItem->id => [
            'spk_item_id' => $spkItem->id,
            'item_type' => 'Sepatu',
            'shoe_brand' => 'Nike',
            'shoe_type' => 'Pegasus',
            'shoe_color' => 'Black-White',
            'shoe_size' => '42',
            'ref_photos' => []
        ]
    ];

    $wos = $spkService->handToWorkshop($spk, $itemsInput, 1);
    $wo = $wos[0];
    
    $wo->update([
        'status' => $status,
        'created_at' => now()->subDays($dateOffsetDays),
        'entry_date' => now()->subDays($dateOffsetDays),
    ]);

    return $wo;
}

$fastCleanList = [
    [
        'id' => $fastClean->id,
        'name' => $fastClean->name,
        'category' => $fastClean->category,
        'price' => $fastClean->price
    ]
];

$multiServiceList = [
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

// ==========================================
// SEEDING PREPARATION (5 Items)
// ==========================================
// 1. Fast Track (Normal)
createTestOrder($lead, $customer, $fastCleanList, 'Normal', WorkOrderStatus::PREPARATION);
// 2. Fast Track (Prioritas)
createTestOrder($lead, $customer, $fastCleanList, 'Prioritas', WorkOrderStatus::PREPARATION);
// 3. Regular (Normal)
createTestOrder($lead, $customer, $multiServiceList, 'Normal', WorkOrderStatus::PREPARATION);
// 4. Regular (Prioritas)
createTestOrder($lead, $customer, $multiServiceList, 'Prioritas', WorkOrderStatus::PREPARATION);
// 5. Fast Track (Overdue/Violated - Normal)
createTestOrder($lead, $customer, $fastCleanList, 'Normal', WorkOrderStatus::PREPARATION, 5);

echo "Prep items seeded.\n";

// ==========================================
// SEEDING PRODUCTION (5 Items)
// ==========================================
// 1. Fast Track (Normal)
$woProd1 = createTestOrder($lead, $customer, $fastCleanList, 'Normal', WorkOrderStatus::PRODUCTION);
$woProd1->update(['prod_sol_started_at' => now()]);
// 2. Fast Track (Prioritas)
$woProd2 = createTestOrder($lead, $customer, $fastCleanList, 'Prioritas', WorkOrderStatus::PRODUCTION);
$woProd2->update(['prod_sol_started_at' => now()]);
// 3. Regular (Normal)
$woProd3 = createTestOrder($lead, $customer, $multiServiceList, 'Normal', WorkOrderStatus::PRODUCTION);
$woProd3->update(['prod_sol_started_at' => now()]);
// 4. Regular (Prioritas)
$woProd4 = createTestOrder($lead, $customer, $multiServiceList, 'Prioritas', WorkOrderStatus::PRODUCTION);
$woProd4->update(['prod_sol_started_at' => now()]);
// 5. Fast Track (Overdue/Violated - Prioritas)
$woProd5 = createTestOrder($lead, $customer, $fastCleanList, 'Prioritas', WorkOrderStatus::PRODUCTION, 6);
$woProd5->update(['prod_sol_started_at' => now()->subDays(5)]);

echo "Prod items seeded.\n";

// ==========================================
// SEEDING QC (5 Items)
// ==========================================
// 1. Fast Track (Normal)
$woQc1 = createTestOrder($lead, $customer, $fastCleanList, 'Normal', WorkOrderStatus::QC);
$woQc1->update(['qc_jahit_started_at' => now()]);
// 2. Fast Track (Prioritas)
$woQc2 = createTestOrder($lead, $customer, $fastCleanList, 'Prioritas', WorkOrderStatus::QC);
$woQc2->update(['qc_jahit_started_at' => now()]);
// 3. Regular (Normal)
$woQc3 = createTestOrder($lead, $customer, $multiServiceList, 'Normal', WorkOrderStatus::QC);
$woQc3->update(['qc_jahit_started_at' => now()]);
// 4. Regular (Prioritas)
$woQc4 = createTestOrder($lead, $customer, $multiServiceList, 'Prioritas', WorkOrderStatus::QC);
$woQc4->update(['qc_jahit_started_at' => now()]);
// 5. Fast Track (Overdue/Violated - Normal)
$woQc5 = createTestOrder($lead, $customer, $fastCleanList, 'Normal', WorkOrderStatus::QC, 7);
$woQc5->update(['qc_jahit_started_at' => now()->subDays(6)]);

echo "QC items seeded.\n";

echo "Seeding completed successfully!\n";
