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

echo "Seeding Jasa Biasa and OTO dummy data...\n";

// 1. Ensure Services
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
}

// 2. Helper to create base SPK and WorkOrder
function createDummySpkAndHandover($name, $phone, $servicesList, $priority = 'Normal') {
    $normalizedPhone = \App\Helpers\PhoneHelper::normalize($phone);
    $customer = Customer::where('phone', $normalizedPhone)->first();
    if (!$customer) {
        $customer = Customer::create([
            'phone' => $phone,
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@gmail.com',
            'address' => 'Jl. Dummy Pengerjaan No. 123'
        ]);
    }

    $lead = CsLead::where('customer_phone', $phone)
                  ->orWhere('customer_phone', $normalizedPhone)
                  ->first();
    if (!$lead) {
        $lead = CsLead::create([
            'customer_phone' => $phone,
            'customer_name' => $name,
            'customer_email' => strtolower(str_replace(' ', '.', $name)) . '@gmail.com',
            'customer_address' => 'Jl. Dummy Pengerjaan No. 123',
            'status' => 'CONVERTED',
            'cs_id' => 1,
            'channel' => 'ONLINE',
            'source' => 'WhatsApp'
        ]);
    }

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
        'expected_delivery_date' => now()->addDays(3),
    ]);

    // Create CsSpkItem
    $spkItem = CsSpkItem::create([
        'spk_id' => $spk->id,
        'category' => 'Sepatu',
        'shoe_brand' => 'Adidas',
        'shoe_type' => 'Ultraboost',
        'shoe_color' => 'White',
        'shoe_size' => '43',
        'services' => $servicesList,
        'item_total_price' => collect($servicesList)->sum('price'),
        'original_price' => collect($servicesList)->sum('price'),
        'discount_amount' => 0,
        'status' => CsSpkItem::STATUS_PENDING,
        'hk_days' => 3,
    ]);

    // Format handover
    $itemsInput = [
        $spkItem->id => [
            'spk_item_id' => $spkItem->id,
            'item_type' => 'Sepatu',
            'shoe_brand' => 'Adidas',
            'shoe_type' => 'Ultraboost',
            'shoe_color' => 'White',
            'shoe_size' => '43',
            'ref_photos' => []
        ]
    ];

    $wos = $spkService->handToWorkshop($spk, $itemsInput, 1);
    return $wos[0];
}

// ==========================================
// 1. DATA DUMMY JASA BIASA
// ==========================================
$serviceBiasa = [
    [
        'id' => $deepClean->id,
        'name' => $deepClean->name,
        'category' => $deepClean->category,
        'price' => $deepClean->price
    ]
];
$woBiasa = createDummySpkAndHandover('Ronaldo JasaBiasa', '081111111111', $serviceBiasa, 'Normal');
$woBiasa->status = WorkOrderStatus::SORTIR;
$woBiasa->save();
echo "Jasa Biasa SPK created: {$woBiasa->spk_number} | Priority: {$woBiasa->priority} | fast_track_status: {$woBiasa->fast_track_status}\n";

// ==========================================
// 2. DATA DUMMY OTO SPK
// ==========================================
$serviceOto = [
    [
        'id' => $reglueSol->id,
        'name' => $reglueSol->name,
        'category' => $reglueSol->category,
        'price' => $reglueSol->price
    ]
];
$woOto = createDummySpkAndHandover('Messi OTO', '082222222222', $serviceOto, 'Normal');
$woOto->status = WorkOrderStatus::SORTIR;
$woOto->priority = 'OTO';
$woOto->has_active_oto = true;
$woOto->cost_oto = 80000;
$woOto->save();

// Create associated OTO record
$otoRecord = \App\Models\OTO::create([
    'work_order_id' => $woOto->id,
    'spk_number' => $woOto->spk_number,
    'customer_name' => $woOto->customer_name,
    'customer_phone' => $woOto->customer_phone,
    'title' => 'Penawaran Jasa Tambahan OTO untuk ' . $woOto->customer_name,
    'description' => 'Penawaran upgrade cuci premium + recolor sol',
    'oto_type' => 'UPSELL',
    'proposed_services' => 'Recolor Sol',
    'total_normal_price' => 'Rp. 100.000',
    'total_oto_price' => 'Rp. 80.000',
    'total_discount' => 'Rp. 20.000',
    'discount_percent' => 20.00,
    'estimated_days' => 1,
    'valid_until' => now()->addDays(2),
    'status' => 'ACCEPTED',
    'dp_required' => 'Rp. 40.000',
    'dp_paid' => true,
    'dp_paid_at' => now(),
    'created_by' => 1,
]);

echo "OTO SPK created: {$woOto->spk_number} | Priority: {$woOto->priority} | fast_track_status: {$woOto->fast_track_status} | has_active_oto: {$woOto->has_active_oto}\n";

echo "All dummy data seeded successfully!\n";
