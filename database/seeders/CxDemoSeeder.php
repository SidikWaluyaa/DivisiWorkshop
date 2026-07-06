<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\OrderPayment;
use App\Models\StorageRack;
use App\Models\StorageAssignment;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CxDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup / ensure core actors exist
        $admin = User::updateOrCreate(
            ['email' => 'admin@workshop.com'],
            [
                'name' => 'Admin Gudang',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $csUser = User::updateOrCreate(
            ['email' => 'cs@example.com'],
            [
                'name' => 'CS Demo',
                'password' => bcrypt('password'),
                'role' => 'cs',
                'access_rights' => ['cs'],
                'cs_code' => 'CS1',
            ]
        );

        $techUser = User::updateOrCreate(
            ['email' => 'tech@workshop.com'],
            [
                'name' => 'Dr. Shoe (Tech)',
                'password' => bcrypt('password'),
                'role' => 'technician',
            ]
        );

        // 2. Fetch some services to attach to work orders
        $services = Service::all();
        if ($services->isEmpty()) {
            // Fallback: create basic services if not seeded
            $services = collect([
                Service::create(['name' => 'Fast Clean', 'category' => 'Cleaning', 'price' => 35000]),
                Service::create(['name' => 'Deep Clean', 'category' => 'Cleaning', 'price' => 75000]),
                Service::create(['name' => 'Reglue Sol', 'category' => 'Sol Repair', 'price' => 50000]),
                Service::create(['name' => 'Repaint Standard', 'category' => 'Repaint', 'price' => 120000]),
                Service::create(['name' => 'Unyellowing', 'category' => 'Unyellowing', 'price' => 60000]),
            ]);
        }

        // 3. Clear existing WorkOrders, Invoices, OrderPayments, and CxIssues to rebuild cleanly
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CxIssue::truncate();
        OrderPayment::truncate();
        Invoice::truncate();
        WorkOrder::truncate();
        Customer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Generate Customers
        $customerNames = [
            'Sidik Waluya', 'Budi Santoso', 'Jane Doe', 'Adit Prasetyo', 'Citra Kirana',
            'Dewi Lestari', 'Eko Raharjo', 'Fajar Utama', 'Gita Gutawa', 'Hadi Wijaya'
        ];
        $customerPhones = [
            '6281214696299', '6281234567890', '6289876543210', '6281112223334', '6281998877665',
            '6285711223344', '6281299887766', '6287811223344', '6281311223344', '6281511223344'
        ];

        $customers = collect();
        for ($i = 0; $i < count($customerNames); $i++) {
            $customers->push(Customer::create([
                'name' => $customerNames[$i],
                'phone' => $customerPhones[$i],
                'address' => 'Jl. Kebon Jeruk No. ' . ($i + 5),
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'address_verified_at' => now(),
            ]));
        }

        // 5. Generate WorkOrders & Invoices & Payments

        // --- GROUP 1: CX Follow Up (Active & Open issues) ---
        // Record 1: The exact SPK from the screenshot
        $wo1 = WorkOrder::create([
            'spk_number' => 'S-2607-01-0002-SW',
            'customer_name' => 'Sidik',
            'customer_phone' => '6281214696299',
            'shoe_brand' => 'Puma',
            'shoe_type' => 'Sneakers',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::create(2026, 7, 6, 9, 26, 0),
            'estimation_date' => Carbon::create(2026, 7, 18, 0, 0, 0),
            'created_by' => $admin->id,
            'total_transaksi' => 150000,
            'total_paid' => 75000,
            'sisa_tagihan' => 75000,
            'status_pembayaran' => 'DP/Cicil',
        ]);
        $wo1->services()->attach($services->first()->id, ['cost' => $services->first()->price, 'technician_id' => $techUser->id, 'status' => 'PENDING']);
        
        CxIssue::create([
            'work_order_id' => $wo1->id,
            'reported_by' => $admin->id,
            'type' => 'TEKNIS',
            'source' => 'WORKSHOP',
            'category' => 'TEKNIS',
            'kendala' => 'Sol Melepas',
            'opsi_solusi' => 'Tambah Jasa Reglue',
            'spk_number' => $wo1->spk_number,
            'customer_name' => $wo1->customer_name,
            'customer_phone' => $wo1->customer_phone,
            'status' => 'OPEN',
            'shipping_status' => 'HOLD',
            'created_at' => Carbon::create(2026, 7, 6, 9, 26, 0),
            'updated_at' => Carbon::create(2026, 7, 6, 9, 26, 0),
        ]);

        // Record 2: Active CX issue (Gudang source, SEND status)
        $wo2 = WorkOrder::create([
            'spk_number' => 'S-2607-01-0003-SW',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '6281234567890',
            'shoe_brand' => 'Nike Air Max',
            'shoe_type' => 'Sneakers',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::create(2026, 7, 5, 10, 15, 0),
            'estimation_date' => Carbon::create(2026, 7, 15, 0, 0, 0),
            'created_by' => $admin->id,
            'total_transaksi' => 120000,
            'total_paid' => 120000,
            'sisa_tagihan' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
        $wo2->services()->attach($services->last()->id, ['cost' => $services->last()->price, 'technician_id' => $techUser->id, 'status' => 'PENDING']);
        
        CxIssue::create([
            'work_order_id' => $wo2->id,
            'reported_by' => $admin->id,
            'type' => 'TEKNIS',
            'source' => 'GUDANG',
            'category' => 'TEKNIS',
            'kendala' => 'Bahan upper mengelupas parah sebelum dicuci.',
            'opsi_solusi' => 'Lanjut dengan resiko repaint atau ganti bahan.',
            'spk_number' => $wo2->spk_number,
            'customer_name' => $wo2->customer_name,
            'customer_phone' => $wo2->customer_phone,
            'status' => 'OPEN',
            'shipping_status' => 'SEND',
            'created_at' => Carbon::create(2026, 7, 5, 10, 15, 0),
            'updated_at' => Carbon::create(2026, 7, 5, 10, 15, 0),
        ]);

        // Record 3: Active CX issue (Manual, WARM lead, stuck > 3 days)
        $wo3 = WorkOrder::create([
            'spk_number' => 'S-2607-01-0004-SW',
            'customer_name' => 'Adit Prasetyo',
            'customer_phone' => '6281112223334',
            'shoe_brand' => 'Adidas Yeezy',
            'shoe_type' => 'Sneakers',
            'status' => WorkOrderStatus::CX_FOLLOWUP->value,
            'entry_date' => Carbon::now()->subDays(6),
            'estimation_date' => Carbon::now()->addDays(2),
            'created_by' => $admin->id,
            'total_transaksi' => 180000,
            'total_paid' => 90000,
            'sisa_tagihan' => 90000,
            'status_pembayaran' => 'DP/Cicil',
        ]);
        $wo3->services()->attach($services->random()->id, ['cost' => 90000, 'technician_id' => $techUser->id, 'status' => 'PENDING']);
        
        CxIssue::create([
            'work_order_id' => $wo3->id,
            'reported_by' => $admin->id,
            'type' => 'TEKNIS',
            'source' => 'MANUAL',
            'category' => 'TEKNIS',
            'kendala' => 'Midsole retak-retak setelah dilakukan Unyellowing.',
            'opsi_solusi' => 'Perlu jasa tambahan Recolor Midsole.',
            'spk_number' => $wo3->spk_number,
            'customer_name' => $wo3->customer_name,
            'customer_phone' => $wo3->customer_phone,
            'status' => 'OPEN',
            'shipping_status' => 'HOLD',
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ]);


        // --- GROUP 2: SPK Pending (CS) ---
        for ($i = 0; $i < 3; $i++) {
            $cust = $customers->get($i + 3);
            WorkOrder::create([
                'spk_number' => 'S-2607-01-001' . $i . '-CS',
                'customer_name' => $cust->name,
                'customer_phone' => $cust->phone,
                'shoe_brand' => 'Nike Air Force 1',
                'shoe_type' => 'Sneakers',
                'status' => WorkOrderStatus::SPK_PENDING->value,
                'entry_date' => Carbon::now()->subHours($i * 4),
                'estimation_date' => Carbon::now()->addDays(5),
                'created_by' => $csUser->id,
                'total_transaksi' => 75000,
                'total_paid' => 0,
                'sisa_tagihan' => 75000,
                'status_pembayaran' => 'Belum Bayar',
            ]);
        }


        // --- GROUP 3: Assessment / Inbound ---
        for ($i = 0; $i < 3; $i++) {
            $cust = $customers->get($i + 5);
            $wo = WorkOrder::create([
                'spk_number' => 'S-2607-01-002' . $i . '-GD',
                'customer_name' => $cust->name,
                'customer_phone' => $cust->phone,
                'shoe_brand' => 'Converse Chuck Taylor',
                'shoe_type' => 'Canvas',
                'status' => WorkOrderStatus::ASSESSMENT->value,
                'entry_date' => Carbon::now()->subDays(1),
                'estimation_date' => Carbon::now()->addDays(4),
                'created_by' => $admin->id,
                'total_transaksi' => 50000,
                'total_paid' => 25000,
                'sisa_tagihan' => 25000,
                'status_pembayaran' => 'DP/Cicil',
            ]);
            $wo->services()->attach($services->random()->id, ['cost' => 50000, 'technician_id' => null, 'status' => 'PENDING']);
        }


        // --- GROUP 4: Production (WS Hijau) ---
        $brands = ['New Balance 574', 'Adidas Samba', 'Vans Old Skool', 'Jordan 1 Low'];
        for ($i = 0; $i < count($brands); $i++) {
            $cust = $customers->get($i);
            $wo = WorkOrder::create([
                'spk_number' => 'S-2607-01-003' . $i . '-SW',
                'customer_name' => $cust->name,
                'customer_phone' => $cust->phone,
                'shoe_brand' => $brands[$i],
                'shoe_type' => 'Sneakers',
                'status' => WorkOrderStatus::PRODUCTION->value,
                'entry_date' => Carbon::now()->subDays(2),
                'estimation_date' => Carbon::now()->addDays(3),
                'created_by' => $admin->id,
                'total_transaksi' => 120000,
                'total_paid' => 60000,
                'sisa_tagihan' => 60000,
                'status_pembayaran' => 'DP/Cicil',
            ]);
            $wo->services()->attach($services->random()->id, ['cost' => 120000, 'technician_id' => $techUser->id, 'status' => 'PENDING']);
        }


        // --- GROUP 5: Quality Control (QC) ---
        for ($i = 0; $i < 2; $i++) {
            $cust = $customers->get($i + 2);
            $wo = WorkOrder::create([
                'spk_number' => 'S-2607-01-004' . $i . '-SW',
                'customer_name' => $cust->name,
                'customer_phone' => $cust->phone,
                'shoe_brand' => 'Vans Slip On',
                'shoe_type' => 'Canvas',
                'status' => WorkOrderStatus::QC->value,
                'entry_date' => Carbon::now()->subDays(3),
                'estimation_date' => Carbon::now()->addDays(2),
                'created_by' => $admin->id,
                'total_transaksi' => 60000,
                'total_paid' => 60000,
                'sisa_tagihan' => 0,
                'status_pembayaran' => 'Lunas',
            ]);
            $wo->services()->attach($services->random()->id, ['cost' => 60000, 'technician_id' => $techUser->id, 'status' => 'COMPLETED']);
        }


        // --- GROUP 6: Completed (Selesai/History) ---
        // Jane Doe's completed order with a resolved CX issue
        $woCompleted = WorkOrder::create([
            'spk_number' => 'S-2607-01-0001-SW',
            'customer_name' => 'Jane Doe',
            'customer_phone' => '6289876543210',
            'shoe_brand' => 'Adidas Ultraboost',
            'shoe_type' => 'Running',
            'status' => WorkOrderStatus::HISTORY->value,
            'entry_date' => Carbon::create(2026, 7, 1, 8, 0, 0),
            'estimation_date' => Carbon::create(2026, 7, 10, 0, 0, 0),
            'created_by' => $admin->id,
            'total_transaksi' => 175000,
            'total_paid' => 175000,
            'sisa_tagihan' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
        $woCompleted->services()->attach($services->first()->id, ['cost' => 175000, 'technician_id' => $techUser->id, 'status' => 'COMPLETED']);
        
        CxIssue::create([
            'work_order_id' => $woCompleted->id,
            'reported_by' => $admin->id,
            'type' => 'TEKNIS',
            'source' => 'WORKSHOP',
            'category' => 'TEKNIS',
            'kendala' => 'Benang jahitan lepas setelah dicuci.',
            'opsi_solusi' => 'Jahit ulang gratis.',
            'spk_number' => $woCompleted->spk_number,
            'customer_name' => $woCompleted->customer_name,
            'customer_phone' => $woCompleted->customer_phone,
            'status' => 'RESOLVED',
            'shipping_status' => 'SEND',
            'resolved_by' => $admin->id,
            'resolved_at' => Carbon::create(2026, 7, 4, 15, 30, 0),
            'resolution_type' => 'lanjut',
            'created_at' => Carbon::create(2026, 7, 1, 8, 0, 0),
            'updated_at' => Carbon::create(2026, 7, 4, 15, 30, 0),
        ]);


        // --- GROUP 7: Generate Invoices & OrderPayments for financial dashboard ---
        // Let's create an Invoice for all unpaid/partially paid WorkOrders
        $unpaidWos = WorkOrder::where('status_pembayaran', '!=', 'Lunas')->get();
        foreach ($unpaidWos as $index => $wo) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-2607-' . Str::padLeft($index + 1, 4, '0'),
                'customer_id' => $customers->random()->id,
                'total_amount' => $wo->total_transaksi,
                'paid_amount' => $wo->total_paid,
                'discount' => 0,
                'shipping_cost' => 0,
                'status' => $wo->status_pembayaran === 'Belum Bayar' ? 'Belum Bayar' : 'DP/Cicil',
                'due_date' => Carbon::now()->addDays(7),
                'estimasi_selesai' => $wo->estimation_date,
                'notes' => 'Invoice auto-generated for testing.',
                'target_dp_amount' => $wo->total_transaksi * 0.5,
                'dp_unique_code' => rand(100, 999),
                'final_unique_code' => rand(100, 999),
            ]);

            // Update WorkOrder to point to Invoice
            $wo->update(['invoice_id' => $invoice->id]);

            // If it has DP, create a payment record
            if ($wo->total_paid > 0) {
                OrderPayment::create([
                    'work_order_id' => $wo->id,
                    'invoice_id' => $invoice->id,
                    'spk_number_snapshot' => $wo->spk_number,
                    'type' => 'before',
                    'pic_id' => $admin->id,
                    'amount_total' => $wo->total_paid,
                    'amount_service' => $wo->total_paid,
                    'amount_shipping' => 0,
                    'payment_method' => 'Transfer Bank',
                    'paid_at' => Carbon::now()->subDays(1),
                    'is_verified' => true,
                    'customer_name_snapshot' => $wo->customer_name,
                    'customer_phone_snapshot' => $wo->customer_phone,
                    'total_bill_snapshot' => $wo->total_transaksi,
                ]);
            }
        }

        // Add rack assignments
        $racks = StorageRack::all();
        if ($racks->isNotEmpty()) {
            $racksCount = $racks->count();
            // Let's assign some work orders to racks
            $activeWos = WorkOrder::where('status', '!=', WorkOrderStatus::HISTORY->value)
                ->where('status', '!=', WorkOrderStatus::BATAL->value)
                ->limit(8)
                ->get();
            
            foreach ($activeWos as $idx => $aWo) {
                $rack = $racks->get($idx % $racksCount);
                
                StorageAssignment::create([
                    'work_order_id' => $aWo->id,
                    'rack_code' => $rack->rack_code,
                    'item_type' => 'shoes',
                    'stored_at' => Carbon::now()->subDays(1),
                    'stored_by' => $admin->id,
                    'status' => 'stored',
                ]);

                $aWo->update([
                    'storage_rack_code' => $rack->rack_code,
                    'stored_at' => Carbon::now()->subDays(1)
                ]);
                $rack->increment('current_count');
            }
        }
    }
}
