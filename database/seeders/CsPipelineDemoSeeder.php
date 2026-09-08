<?php

namespace Database\Seeders;

use App\Enums\WorkOrderStatus;
use App\Models\CsActivity;
use App\Models\CsLead;
use App\Models\CsQuotation;
use App\Models\CsQuotationItem;
use App\Models\CsSpk;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CsPipelineDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $csUser = User::where('role', 'cs')->first() ?? User::find(6) ?? User::first();
            $csId = $csUser->id;
            $now = Carbon::now();

            // -------------------------------------------------------------
            // 1. PIPELINE: GREETING (3 Leads)
            // -------------------------------------------------------------
            $greetings = [
                [
                    'name' => 'Fajar Nugraha',
                    'phone' => '081234567801',
                    'email' => 'fajar.nugraha@gmail.com',
                    'source' => CsLead::SOURCE_WHATSAPP,
                    'channel' => CsLead::CHANNEL_ONLINE,
                    'priority' => CsLead::PRIORITY_WARM,
                    'notes' => 'Tanya servis sol copot dan repaint untuk sepatu Jordan 1 High.',
                    'brand' => 'Nike',
                    'type' => 'Air Jordan 1 High',
                    'color' => 'Chicago (Red/White/Black)',
                    'size' => '42',
                    'expected_value' => 450000,
                    'activity_chat' => 'Halo min, mau tanya kalau sol sepatu Jordan 1 saya lepas dan kulitnya agak pudar bisa diperbaiki?',
                    'activity_reply' => 'Halo Kak Fajar! Tentu bisa banget kak. Boleh kirimkan foto detail sepatu dan kondisi solnya agar kami bantu estimasi ya kak.',
                ],
                [
                    'name' => 'Siti Nurhaliza',
                    'phone' => '081388997702',
                    'email' => 'siti.nurhaliza@yahoo.com',
                    'source' => CsLead::SOURCE_INSTAGRAM,
                    'channel' => CsLead::CHANNEL_ONLINE,
                    'priority' => CsLead::PRIORITY_COLD,
                    'notes' => 'Inquiry treatment deep cleaning dan unyellowing midsole Samba.',
                    'brand' => 'Adidas',
                    'type' => 'Samba OG',
                    'color' => 'White Black',
                    'size' => '38',
                    'expected_value' => 200000,
                    'activity_chat' => 'Kak, treatment buat Adidas Samba yang midsole-nya menguning berapa ya?',
                    'activity_reply' => 'Halo Kak Siti! Untuk Samba kami sarankan paket Deep Clean + Unyellowing Midsole kak. Hasil dijamin cerah kembali.',
                ],
                [
                    'name' => 'Bambang Pamungkas',
                    'phone' => '081122334403',
                    'email' => 'bambang.p@gmail.com',
                    'source' => CsLead::SOURCE_WALKIN,
                    'channel' => CsLead::CHANNEL_OFFLINE,
                    'priority' => CsLead::PRIORITY_HOT,
                    'notes' => 'Datang ke workshop bawa sepatu Vans Old Skool foxing pecah keliling.',
                    'brand' => 'Vans',
                    'type' => 'Old Skool Classic',
                    'color' => 'Black/White',
                    'size' => '43',
                    'expected_value' => 275000,
                    'activity_chat' => 'Customer walk-in konsultasi di front desk mengenai penggantian foxing dan lem ulang.',
                    'activity_reply' => 'CS melakukan pemeriksaan fisik awal dan mencatat keluhan foxing getas/pecah.',
                ],
            ];

            foreach ($greetings as $idx => $g) {
                $createdTime = $now->copy()->subHours(rand(1, 5))->subMinutes(rand(10, 45));
                $lead = CsLead::create([
                    'customer_name' => $g['name'],
                    'customer_phone' => $g['phone'],
                    'customer_email' => $g['email'],
                    'customer_address' => 'Jl. Kebon Jeruk No. ' . ($idx + 12) . ', Jakarta Barat',
                    'customer_city' => 'Jakarta Barat',
                    'customer_province' => 'DKI Jakarta',
                    'status' => CsLead::STATUS_GREETING,
                    'cs_id' => $csId,
                    'channel' => $g['channel'],
                    'source' => $g['source'],
                    'priority' => $g['priority'],
                    'expected_value' => $g['expected_value'],
                    'notes' => $g['notes'],
                    'first_contact_at' => $createdTime,
                    'first_response_at' => $createdTime->copy()->addMinutes(4),
                    'response_time_minutes' => 4,
                    'last_activity_at' => $createdTime->copy()->addMinutes(5),
                    'created_at' => $createdTime,
                    'updated_at' => $createdTime,
                ]);

                // Activity Log 1
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_CHAT,
                    'channel' => $g['channel'],
                    'content' => $g['activity_chat'],
                    'created_at' => $createdTime,
                ]);

                // Activity Log 2
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_CHAT,
                    'channel' => $g['channel'],
                    'content' => $g['activity_reply'],
                    'created_at' => $createdTime->copy()->addMinutes(4),
                ]);
            }

            // -------------------------------------------------------------
            // 2. PIPELINE: KONSULTASI (3 Leads with Quotation Draft)
            // -------------------------------------------------------------
            $konsultasis = [
                [
                    'name' => 'Andi Setiawan',
                    'phone' => '081299881104',
                    'email' => 'andi.setiawan@gmail.com',
                    'source' => CsLead::SOURCE_WHATSAPP,
                    'priority' => CsLead::PRIORITY_HOT,
                    'notes' => 'Sepatu New Balance 990v5 sol aus total. Ingin upgrade ke Vibram Outsole.',
                    'brand' => 'New Balance',
                    'type' => '990v5 Made in USA',
                    'color' => 'Grey',
                    'size' => '43',
                    'service_ids' => [2, 66], // Sol-Vibram (1050k) + Deep Clean (75k) = 1.125k
                    'discount' => 25000,
                ],
                [
                    'name' => 'Clara Maharani',
                    'phone' => '081955443305',
                    'email' => 'clara.m@gmail.com',
                    'source' => CsLead::SOURCE_INSTAGRAM,
                    'priority' => CsLead::PRIORITY_WARM,
                    'notes' => 'Converse Chuck 70 sol samping lepas dan lining tumit sobek.',
                    'brand' => 'Converse',
                    'type' => 'Chuck 70 High',
                    'color' => 'Parchment',
                    'size' => '39',
                    'service_ids' => [38, 16], // Lem Jahit Reguler (190k) + Ganti Lining Mesh (175k) = 365k
                    'discount' => 0,
                ],
                [
                    'name' => 'Deni Kurniawan',
                    'phone' => '087811229906',
                    'email' => 'deni.kurnia@corp.com',
                    'source' => CsLead::SOURCE_REFERRAL,
                    'priority' => CsLead::PRIORITY_HOT,
                    'notes' => 'Boots Red Wing sol pecah. Request Sol Kulit Goodyear Welt.',
                    'brand' => 'Timberland',
                    'type' => '6-Inch Premium Boot',
                    'color' => 'Wheat Nubuck',
                    'size' => '42',
                    'service_ids' => [9, 29], // Sol Kulit Goodyear Welt (1000k) + Upper Treatment (90k) = 1.090k
                    'discount' => 40000,
                ],
            ];

            foreach ($konsultasis as $idx => $k) {
                $createdTime = $now->copy()->subHours(rand(6, 12));
                
                $services = Service::whereIn('id', $k['service_ids'])->get();
                $subtotal = $services->sum('price');
                $total = $subtotal - $k['discount'];

                $lead = CsLead::create([
                    'customer_name' => $k['name'],
                    'customer_phone' => $k['phone'],
                    'customer_email' => $k['email'],
                    'customer_address' => 'Jl. Tebet Raya No. ' . ($idx + 45) . ', Jakarta Selatan',
                    'customer_city' => 'Jakarta Selatan',
                    'customer_province' => 'DKI Jakarta',
                    'status' => CsLead::STATUS_KONSULTASI,
                    'cs_id' => $csId,
                    'channel' => CsLead::CHANNEL_ONLINE,
                    'source' => $k['source'],
                    'priority' => $k['priority'],
                    'expected_value' => $total,
                    'notes' => $k['notes'],
                    'first_contact_at' => $createdTime,
                    'first_response_at' => $createdTime->copy()->addMinutes(3),
                    'response_time_minutes' => 3,
                    'last_activity_at' => $createdTime->copy()->addHours(1),
                    'created_at' => $createdTime,
                    'updated_at' => $createdTime->copy()->addHours(1),
                ]);

                // Create Quotation
                $quotation = CsQuotation::create([
                    'cs_lead_id' => $lead->id,
                    'version' => 1,
                    'subtotal' => $subtotal,
                    'discount' => $k['discount'],
                    'discount_type' => CsQuotation::DISCOUNT_AMOUNT,
                    'total' => $total,
                    'status' => CsQuotation::STATUS_DRAFT,
                    'shoe_brand' => $k['brand'],
                    'shoe_type' => $k['type'],
                    'shoe_color' => $k['color'],
                    'shoe_size' => $k['size'],
                    'notes' => 'Estimasi pengerjaan 7-10 hari kerja. Garansi hasil 30 hari.',
                    'valid_until' => $now->copy()->addDays(7),
                    'created_at' => $createdTime->copy()->addMinutes(30),
                ]);

                // Create Quotation Item
                $servicesArray = $services->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'price' => (float)$s->price,
                    'category' => $s->category,
                ])->toArray();

                CsQuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_number' => 1,
                    'category' => 'Sneakers & Boots',
                    'shoe_brand' => $k['brand'],
                    'shoe_type' => $k['type'],
                    'shoe_color' => $k['color'],
                    'shoe_size' => $k['size'],
                    'condition_notes' => 'Kondisi fisik sudah diperiksa melalui foto. ' . $k['notes'],
                    'services' => $servicesArray,
                    'item_total_price' => $total,
                    'hk_days' => 7,
                    'is_warranty' => true,
                ]);

                // Activity: Diagnosis
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_NOTE,
                    'content' => "Diagnosis CS: Customer membutuhkan pengerjaan {$services->pluck('name')->implode(', ')}. Penawaran awal dibuat Rp " . number_format($total, 0, ',', '.'),
                    'created_at' => $createdTime->copy()->addMinutes(30),
                ]);
            }

            // -------------------------------------------------------------
            // 3. PIPELINE: FOLLOW_UP (3 Leads with Sent Quotations)
            // -------------------------------------------------------------
            $followUps = [
                [
                    'name' => 'Maya Safitri',
                    'phone' => '085677882207',
                    'email' => 'maya.safitri@gmail.com',
                    'source' => CsLead::SOURCE_WHATSAPP,
                    'priority' => CsLead::PRIORITY_HOT,
                    'notes' => 'Quotation sent. Customer minta waktu diskusi dengan suami untuk warna benang jahit.',
                    'brand' => 'Dr. Martens',
                    'type' => '1461 3-Eye Shoe',
                    'color' => 'Cherry Red',
                    'size' => '39',
                    'service_ids' => [1, 24], // Sol-jadi (250k) + Repaint Standard (160k) = 410k
                    'discount' => 0,
                    'next_follow_up' => $now->copy()->startOfDay(), // Due today
                ],
                [
                    'name' => 'Rian Hidayat',
                    'phone' => '081244556608',
                    'email' => 'rian.hidayat@outlook.com',
                    'source' => CsLead::SOURCE_WEBSITE,
                    'priority' => CsLead::PRIORITY_WARM,
                    'notes' => 'Quotation sent. Follow up jadwal drop sepatu ke workshop.',
                    'brand' => 'Asics',
                    'type' => 'Gel-Kayano 14',
                    'color' => 'Silver Cream',
                    'size' => '42',
                    'service_ids' => [30, 38], // Unyellowing (125k) + Lem Jahit (190k) = 315k
                    'discount' => 15000,
                    'next_follow_up' => $now->copy()->addDay(),
                ],
                [
                    'name' => 'Eka Pratiwi',
                    'phone' => '081399001109',
                    'email' => 'eka.pratiwi@bankmandiri.co.id',
                    'source' => CsLead::SOURCE_INSTAGRAM,
                    'priority' => CsLead::PRIORITY_WARM,
                    'notes' => 'Running shoes sol mangap. Menunggu konfirmasi kirim via Paxel.',
                    'brand' => 'On Running',
                    'type' => 'Cloudmonster',
                    'color' => 'All Black',
                    'size' => '41',
                    'service_ids' => [67, 66], // Reglue Sol (50k) + Deep Clean (75k) = 125k
                    'discount' => 0,
                    'next_follow_up' => $now->copy()->addDays(2),
                ],
            ];

            foreach ($followUps as $idx => $fu) {
                $createdTime = $now->copy()->subDays(rand(1, 2));
                
                $services = Service::whereIn('id', $fu['service_ids'])->get();
                $subtotal = $services->sum('price');
                $total = $subtotal - $fu['discount'];

                $lead = CsLead::create([
                    'customer_name' => $fu['name'],
                    'customer_phone' => $fu['phone'],
                    'customer_email' => $fu['email'],
                    'customer_address' => 'Jl. Senopati No. ' . ($idx + 18) . ', Jakarta Selatan',
                    'customer_city' => 'Jakarta Selatan',
                    'customer_province' => 'DKI Jakarta',
                    'status' => CsLead::STATUS_FOLLOW_UP,
                    'cs_id' => $csId,
                    'channel' => CsLead::CHANNEL_ONLINE,
                    'source' => $fu['source'],
                    'priority' => $fu['priority'],
                    'expected_value' => $total,
                    'notes' => $fu['notes'],
                    'first_contact_at' => $createdTime,
                    'first_response_at' => $createdTime->copy()->addMinutes(2),
                    'response_time_minutes' => 2,
                    'next_follow_up_at' => $fu['next_follow_up'],
                    'last_activity_at' => $createdTime->copy()->addHours(3),
                    'created_at' => $createdTime,
                    'updated_at' => $createdTime->copy()->addHours(3),
                ]);

                // Create Quotation
                $quotation = CsQuotation::create([
                    'cs_lead_id' => $lead->id,
                    'version' => 1,
                    'subtotal' => $subtotal,
                    'discount' => $fu['discount'],
                    'discount_type' => CsQuotation::DISCOUNT_AMOUNT,
                    'total' => $total,
                    'status' => CsQuotation::STATUS_SENT,
                    'sent_at' => $createdTime->copy()->addHours(1),
                    'shoe_brand' => $fu['brand'],
                    'shoe_type' => $fu['type'],
                    'shoe_color' => $fu['color'],
                    'shoe_size' => $fu['size'],
                    'notes' => 'Penawaran resmi telah dikirimkan via WhatsApp.',
                    'valid_until' => $now->copy()->addDays(5),
                    'created_at' => $createdTime->copy()->addHours(1),
                ]);

                // Quotation Item
                $servicesArray = $services->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'price' => (float)$s->price,
                    'category' => $s->category,
                ])->toArray();

                CsQuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_number' => 1,
                    'category' => 'Sneakers',
                    'shoe_brand' => $fu['brand'],
                    'shoe_type' => $fu['type'],
                    'shoe_color' => $fu['color'],
                    'shoe_size' => $fu['size'],
                    'condition_notes' => $fu['notes'],
                    'services' => $servicesArray,
                    'item_total_price' => $total,
                    'hk_days' => 5,
                    'is_warranty' => true,
                ]);

                // Activities
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_QUOTATION_SENT,
                    'content' => "Quotation #{$quotation->quotation_number} dikirim ke customer sebesar Rp " . number_format($total, 0, ',', '.'),
                    'created_at' => $createdTime->copy()->addHours(1),
                ]);

                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_CHAT,
                    'content' => "Follow Up CS: Menghubungi customer menanyakan kepastian waktu pengiriman sepatu.",
                    'created_at' => $createdTime->copy()->addHours(3),
                ]);
            }

            // -------------------------------------------------------------
            // 4. PIPELINE: CLOSING (2 Leads - Accepted, Ready for SPK)
            // -------------------------------------------------------------
            $closings = [
                [
                    'name' => 'Guntur Pratama',
                    'phone' => '081288776610',
                    'email' => 'guntur.pratama@gmail.com',
                    'source' => CsLead::SOURCE_WHATSAPP,
                    'priority' => CsLead::PRIORITY_HOT,
                    'notes' => 'Customer setuju penawaran. Sedang proses pembayaran DP 50%.',
                    'brand' => 'Salomon',
                    'type' => 'XT-6 Gore-Tex',
                    'color' => 'Phantom/Black',
                    'size' => '44',
                    'service_ids' => [3, 26], // Ganti Outsole Reguler (250k) + Repaint Premium (375k) = 625k
                    'discount' => 25000,
                ],
                [
                    'name' => 'Nadia Putri',
                    'phone' => '081933221111',
                    'email' => 'nadia.putri@gmail.com',
                    'source' => CsLead::SOURCE_WHATSAPP,
                    'priority' => CsLead::PRIORITY_HOT,
                    'notes' => 'Deal penawaran Repaint + Deep Clean. Sepatu sudah dikirim via GoSend ke workshop.',
                    'brand' => 'Nike',
                    'type' => 'Dunk Low Panda',
                    'color' => 'White/Black',
                    'size' => '37',
                    'service_ids' => [24, 66], // Repaint Standard (160k) + Deep Clean (75k) = 235k
                    'discount' => 0,
                ],
            ];

            foreach ($closings as $idx => $cl) {
                $createdTime = $now->copy()->subDays(rand(2, 3));
                
                $services = Service::whereIn('id', $cl['service_ids'])->get();
                $subtotal = $services->sum('price');
                $total = $subtotal - $cl['discount'];

                $lead = CsLead::create([
                    'customer_name' => $cl['name'],
                    'customer_phone' => $cl['phone'],
                    'customer_email' => $cl['email'],
                    'customer_address' => 'Jl. Panglima Polim No. ' . ($idx + 77) . ', Jakarta Selatan',
                    'customer_city' => 'Jakarta Selatan',
                    'customer_province' => 'DKI Jakarta',
                    'status' => CsLead::STATUS_CLOSING,
                    'cs_id' => $csId,
                    'channel' => CsLead::CHANNEL_ONLINE,
                    'source' => $cl['source'],
                    'priority' => $cl['priority'],
                    'expected_value' => $total,
                    'notes' => $cl['notes'],
                    'first_contact_at' => $createdTime,
                    'first_response_at' => $createdTime->copy()->addMinutes(2),
                    'response_time_minutes' => 2,
                    'last_activity_at' => $now->copy()->subHours(rand(1, 3)),
                    'created_at' => $createdTime,
                    'updated_at' => $now->copy()->subHours(rand(1, 3)),
                ]);

                // Create Quotation
                $quotation = CsQuotation::create([
                    'cs_lead_id' => $lead->id,
                    'version' => 1,
                    'subtotal' => $subtotal,
                    'discount' => $cl['discount'],
                    'discount_type' => CsQuotation::DISCOUNT_AMOUNT,
                    'total' => $total,
                    'status' => CsQuotation::STATUS_ACCEPTED,
                    'sent_at' => $createdTime->copy()->addHours(2),
                    'responded_at' => $now->copy()->subHours(4),
                    'shoe_brand' => $cl['brand'],
                    'shoe_type' => $cl['type'],
                    'shoe_color' => $cl['color'],
                    'shoe_size' => $cl['size'],
                    'notes' => 'Customer menyetujui quotation. Menunggu konfirmasi fisik tiba.',
                    'valid_until' => $now->copy()->addDays(7),
                    'created_at' => $createdTime->copy()->addHours(2),
                ]);

                // Quotation Item
                $servicesArray = $services->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'price' => (float)$s->price,
                    'category' => $s->category,
                ])->toArray();

                CsQuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_number' => 1,
                    'category' => 'Sneakers',
                    'shoe_brand' => $cl['brand'],
                    'shoe_type' => $cl['type'],
                    'shoe_color' => $cl['color'],
                    'shoe_size' => $cl['size'],
                    'condition_notes' => $cl['notes'],
                    'services' => $servicesArray,
                    'item_total_price' => $total,
                    'hk_days' => 6,
                    'is_warranty' => true,
                ]);

                // Activities
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_QUOTATION_SENT,
                    'content' => "Quotation #{$quotation->quotation_number} dikirim ke customer.",
                    'created_at' => $createdTime->copy()->addHours(2),
                ]);

                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_QUOTATION_ACCEPTED,
                    'content' => "Customer menyetujui penawaran harga Rp " . number_format($total, 0, ',', '.') . ". Status lead berpindah ke CLOSING.",
                    'created_at' => $now->copy()->subHours(4),
                ]);
            }

            // -------------------------------------------------------------
            // 5. CONVERTED TO WORK ORDERS (3 Leads with Full Lifecycle into Workshop)
            // -------------------------------------------------------------
            $convertedLeads = [
                [
                    'name' => 'Hendro Wicaksono',
                    'phone' => '081277112212',
                    'email' => 'hendro.w@gmail.com',
                    'brand' => 'Nike',
                    'type' => 'Air Max 97',
                    'color' => 'Silver Bullet',
                    'size' => '43',
                    'service_ids' => [38, 66], // Lem Jahit (190k) + Deep Clean (75k) = 265k
                    'wo_status' => WorkOrderStatus::DITERIMA,
                    'stage' => 'Diterima di Gudang Sortir',
                    'days_ago' => 2,
                    'entry_date' => '2026-09-06 09:30:00',
                    'est_date' => '2026-09-13 17:00:00',
                ],
                [
                    'name' => 'Taufik Hidayat',
                    'phone' => '081366554413',
                    'email' => 'taufik.hidayat@badminton.id',
                    'brand' => 'Yonex',
                    'type' => 'Power Cushion 65Z',
                    'color' => 'White/Orange',
                    'size' => '42',
                    'service_ids' => [1, 65], // Sol-jadi (250k) + Fast Clean (35k) = 285k
                    'wo_status' => WorkOrderStatus::PREPARATION,
                    'stage' => 'Tahap Preparation (Pencucian & Bongkar Sol)',
                    'days_ago' => 3,
                    'entry_date' => '2026-09-05 11:15:00',
                    'est_date' => '2026-09-12 17:00:00',
                ],
                [
                    'name' => 'Bayu Samudra',
                    'phone' => '081199887714',
                    'email' => 'bayu.samudra@pelindo.co.id',
                    'brand' => 'Timberland',
                    'type' => 'Yellow Boot Classic',
                    'color' => 'Wheat',
                    'size' => '44',
                    'service_ids' => [2, 19], // Sol Vibram (1050k) + Lapis Kulit (150k) = 1.200k
                    'wo_status' => WorkOrderStatus::PRODUCTION,
                    'stage' => 'Tahap Produksi (Pemasangan Sol Vibram oleh Teknisi)',
                    'days_ago' => 4,
                    'entry_date' => '2026-09-04 14:00:00',
                    'est_date' => '2026-09-11 17:00:00',
                ],
            ];

            foreach ($convertedLeads as $idx => $conv) {
                $createdTime = $now->copy()->subDays($conv['days_ago'] + 1);
                $services = Service::whereIn('id', $conv['service_ids'])->get();
                $total = $services->sum('price');
                $spkNumber = CsSpk::generateSpkNumber('Online', 'CS');

                // 1. Create or Find Customer
                $customer = Customer::firstOrCreate(
                    ['phone' => $conv['phone']],
                    [
                        'name' => $conv['name'],
                        'email' => $conv['email'],
                        'address' => 'Jl. Boulevard Raya Blok M No. ' . ($idx + 5),
                        'city' => 'Jakarta Selatan',
                        'province' => 'DKI Jakarta',
                    ]
                );

                // 2. Create WorkOrder in Workshop
                $workOrder = WorkOrder::create([
                    'spk_number' => $spkNumber,
                    'customer_name' => $conv['name'],
                    'customer_phone' => $conv['phone'],
                    'customer_email' => $conv['email'],
                    'customer_address' => $customer->address,
                    'shoe_brand' => $conv['brand'],
                    'shoe_type' => $conv['type'],
                    'shoe_color' => $conv['color'],
                    'shoe_size' => $conv['size'],
                    'category' => 'Reparasi Sol & Perawatan',
                    'status' => $conv['wo_status'],
                    'channel' => 'ONLINE',
                    'entry_date' => Carbon::parse($conv['entry_date']),
                    'estimation_date' => Carbon::parse($conv['est_date']),
                    'priority' => 'REGULER',
                    'created_by' => $csId,
                    'notes' => 'Order hasil konversi dari CS Lead. ' . $conv['stage'],
                    'total_amount_due' => 0, // Sudah DP / Pelunasan
                    'payment_status' => 'LUNAS',
                    'created_at' => Carbon::parse($conv['entry_date']),
                    'updated_at' => $now,
                ]);

                // 3. Attach WorkOrder Services
                foreach ($services as $srv) {
                    $workOrder->workOrderServices()->create([
                        'service_id' => $srv->id,
                        'custom_service_name' => $srv->name,
                        'category_name' => $srv->category,
                        'cost' => $srv->price,
                        'status' => 'PENDING',
                    ]);
                }
                $workOrder->recalculateTotalPrice();

                // 4. Create CsLead (Status CONVERTED)
                $lead = CsLead::create([
                    'customer_name' => $conv['name'],
                    'customer_phone' => $conv['phone'],
                    'customer_email' => $conv['email'],
                    'customer_address' => $customer->address,
                    'customer_city' => $customer->city,
                    'customer_province' => $customer->province,
                    'status' => CsLead::STATUS_CONVERTED,
                    'cs_id' => $csId,
                    'channel' => CsLead::CHANNEL_ONLINE,
                    'source' => CsLead::SOURCE_WHATSAPP,
                    'priority' => CsLead::PRIORITY_HOT,
                    'expected_value' => $total,
                    'notes' => "Berhasil Closing & SPK terbit: {$spkNumber}. {$conv['stage']}",
                    'converted_to_work_order_id' => $workOrder->id,
                    'first_contact_at' => $createdTime,
                    'first_response_at' => $createdTime->copy()->addMinutes(1),
                    'response_time_minutes' => 1,
                    'last_activity_at' => Carbon::parse($conv['entry_date']),
                    'created_at' => $createdTime,
                    'updated_at' => Carbon::parse($conv['entry_date']),
                ]);

                // 5. Create Quotation & Items
                $quotation = CsQuotation::create([
                    'cs_lead_id' => $lead->id,
                    'version' => 1,
                    'subtotal' => $total,
                    'discount' => 0,
                    'discount_type' => CsQuotation::DISCOUNT_AMOUNT,
                    'total' => $total,
                    'status' => CsQuotation::STATUS_ACCEPTED,
                    'sent_at' => $createdTime->copy()->addHours(1),
                    'responded_at' => $createdTime->copy()->addHours(3),
                    'shoe_brand' => $conv['brand'],
                    'shoe_type' => $conv['type'],
                    'shoe_color' => $conv['color'],
                    'shoe_size' => $conv['size'],
                    'notes' => 'Quotation disetujui customer. SPK langsung dibuat.',
                    'valid_until' => $now->copy()->addDays(7),
                    'created_at' => $createdTime->copy()->addHours(1),
                ]);

                $servicesArray = $services->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'price' => (float)$s->price,
                    'category' => $s->category,
                ])->toArray();

                CsQuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_number' => 1,
                    'category' => 'Sneakers',
                    'shoe_brand' => $conv['brand'],
                    'shoe_type' => $conv['type'],
                    'shoe_color' => $conv['color'],
                    'shoe_size' => $conv['size'],
                    'condition_notes' => 'Sepatu masuk proses workshop.',
                    'services' => $servicesArray,
                    'item_total_price' => $total,
                    'hk_days' => 7,
                    'is_warranty' => true,
                ]);

                // 6. Create CsSpk
                $csSpk = CsSpk::create([
                    'cs_lead_id' => $lead->id,
                    'work_order_id' => $workOrder->id,
                    'spk_number' => $spkNumber,
                    'customer_id' => $customer->id,
                    'services' => $servicesArray,
                    'total_price' => $total,
                    'dp_amount' => $total,
                    'dp_status' => CsSpk::DP_PAID,
                    'dp_paid_at' => Carbon::parse($conv['entry_date']),
                    'payment_method' => 'Transfer BCA',
                    'shoe_brand' => $conv['brand'],
                    'shoe_type' => $conv['type'],
                    'shoe_color' => $conv['color'],
                    'shoe_size' => $conv['size'],
                    'status' => CsSpk::STATUS_HANDED_TO_WORKSHOP,
                    'handed_at' => Carbon::parse($conv['entry_date']),
                    'handed_by' => $csId,
                    'created_at' => Carbon::parse($conv['entry_date']),
                ]);

                // 7. Full Audit Activity Trail
                // Timeline Step 1: Greeting
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_CHAT,
                    'content' => "Customer menghubungi CS via WhatsApp menanyakan estimasi perbaikan {$conv['brand']} {$conv['type']}.",
                    'created_at' => $createdTime,
                ]);

                // Timeline Step 2: Konsultasi & Diagnosis
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_NOTE,
                    'content' => "Diagnosis CS selesai. Layanan yang dibutuhkan: {$services->pluck('name')->implode(', ')}. Total penawaran: Rp " . number_format($total, 0, ',', '.'),
                    'created_at' => $createdTime->copy()->addMinutes(30),
                ]);

                // Timeline Step 3: Quotation Sent
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_QUOTATION_SENT,
                    'content' => "Quotation resmi #{$quotation->quotation_number} dikirimkan ke customer.",
                    'created_at' => $createdTime->copy()->addHours(1),
                ]);

                // Timeline Step 4: Quotation Accepted & DP Paid
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_QUOTATION_ACCEPTED,
                    'content' => "Customer menyetujui penawaran dan melakukan transfer pembayaran penuh sebesar Rp " . number_format($total, 0, ',', '.'),
                    'created_at' => $createdTime->copy()->addHours(3),
                ]);

                // Timeline Step 5: SPK Generated & Handed to Workshop
                CsActivity::create([
                    'cs_lead_id' => $lead->id,
                    'user_id' => $csId,
                    'type' => CsActivity::TYPE_STATUS_CHANGE,
                    'content' => "SPK Resmi #{$spkNumber} berhasil diterbitkan dan fisik sepatu diserahterimakan ke tim Workshop. Status lead: CONVERTED.",
                    'created_at' => Carbon::parse($conv['entry_date']),
                ]);
            }

            DB::commit();
            echo "Successfully generated full CS pipeline dummy data and converted work orders!\n";
        } catch (\Throwable $e) {
            DB::rollBack();
            echo "Seeder failed: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString();
        }
    }
}
