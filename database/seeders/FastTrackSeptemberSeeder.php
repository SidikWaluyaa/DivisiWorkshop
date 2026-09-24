<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\WorkOrderService;
use App\Models\Service;
use App\Models\Customer;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FastTrackSeptemberSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding Fast Track data for September 2026...');

        // 1. Ensure a customer exists
        $customer = Customer::first();
        if (!$customer) {
            $customer = Customer::create([
                'phone' => '081234567899',
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'address' => 'Jl. Sudirman No. 45, Jakarta',
            ]);
        }

        // 2. Ensure Fast Track Service & Regular Service exist
        $ftService = Service::where('allow_fast_track', 'yes')->first();
        if (!$ftService) {
            $ftService = Service::create([
                'name' => 'Fast Clean Express',
                'category' => 'Cleaning',
                'price' => 50000,
                'duration_minutes' => 30,
                'hk_days' => 1,
                'allow_fast_track' => 'yes'
            ]);
        }

        $regService = Service::where('allow_fast_track', 'no')->first();
        if (!$regService) {
            $regService = Service::create([
                'name' => 'Deep Clean Regular',
                'category' => 'Cleaning',
                'price' => 75000,
                'duration_minutes' => 60,
                'hk_days' => 3,
                'allow_fast_track' => 'no'
            ]);
        }

        $shoeBrands = ['Nike', 'Adidas', 'New Balance', 'Vans', 'Converse', 'Salomon', 'Puma', 'Skechers'];
        $shoeTypes = ['SNEAKERS', 'RUNNING', 'CASUAL', 'OUTDOOR', 'SPORT'];

        // A. 22 SPK: FAST TRACK BERHASIL (Clean Run, Selesai on-time, tanpa kendala)
        for ($i = 1; $i <= 22; $i++) {
            $entryDate = Carbon::parse('2026-09-02 08:30:00')->addDays($i % 18)->addHours($i % 6);
            $spkNumber = sprintf('S-2609-%02d-%04d-FT', $entryDate->day, 1000 + $i);

            // Clean fast timeline
            $tPrep = $entryDate->copy()->addHours(6);
            $tSortir = $tPrep->copy()->addHours(12);
            $tProd = $tSortir->copy()->addDays(2);
            $tQC = $tProd->copy()->addHours(18);
            $tSelesai = $tQC->copy()->addHours(4);

            $order = WorkOrder::updateOrCreate(
                ['spk_number' => $spkNumber],
                [
                    'customer_name' => 'Pelanggan FT Sukses ' . $i,
                    'customer_phone' => '081234567890',
                    'shoe_brand' => $shoeBrands[$i % count($shoeBrands)],
                    'shoe_type' => $shoeTypes[$i % count($shoeTypes)],
                    'status' => WorkOrderStatus::SELESAI,
                    'entry_date' => $entryDate,
                    'created_at' => $entryDate,
                    'updated_at' => $tSelesai,
                    'total_transaksi' => 150000 + ($i * 15000),
                ]
            );

            // Attach single FT service
            WorkOrderService::where('work_order_id', $order->id)->delete();
            WorkOrderService::create([
                'work_order_id' => $order->id,
                'service_id' => $ftService->id,
                'cost' => $ftService->price,
            ]);

            // Ensure fast_track_status = 'yes'
            DB::table('work_orders')->where('id', $order->id)->update(['fast_track_status' => 'yes']);

            // Clear old logs and insert status change logs
            $order->logs()->delete();
            $steps = [
                ['step' => 'PREPARATION', 'time' => $tPrep],
                ['step' => 'SORTIR', 'time' => $tSortir],
                ['step' => 'PRODUCTION', 'time' => $tProd],
                ['step' => 'QC', 'time' => $tQC],
                ['step' => 'SELESAI', 'time' => $tSelesai],
            ];

            foreach ($steps as $s) {
                WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'action' => 'STATUS_CHANGE',
                    'step' => $s['step'],
                    'description' => 'Status berubah ke ' . $s['step'],
                    'created_at' => $s['time'],
                    'updated_at' => $s['time'],
                ]);
            }
        }

        // B. 18 SPK: FAST TRACK GAGAL SLA (Melewati batas waktu stasiun)
        for ($i = 1; $i <= 18; $i++) {
            $entryDate = Carbon::parse('2026-09-03 09:00:00')->addDays($i % 16);
            $spkNumber = sprintf('S-2609-%02d-%04d-SLA', $entryDate->day, 2000 + $i);

            // Timeline with SLA violation (misal Production > 4 hari atau Sortir > 3 hari)
            $tPrep = $entryDate->copy()->addDays(2); // Prep > 1 hari (Violation!)
            $tSortir = $tPrep->copy()->addDays(4);   // Sortir > 3 hari (Violation!)
            $tProd = $tSortir->copy()->addDays(5);    // Prod > 4 hari (Violation!)
            $tQC = $tProd->copy()->addDays(2);       // QC > 1 hari (Violation!)
            $tSelesai = $tQC->copy()->addHours(6);

            $order = WorkOrder::updateOrCreate(
                ['spk_number' => $spkNumber],
                [
                    'customer_name' => 'Pelanggan FT Telat ' . $i,
                    'customer_phone' => '081234567890',
                    'shoe_brand' => $shoeBrands[($i + 2) % count($shoeBrands)],
                    'shoe_type' => $shoeTypes[($i + 1) % count($shoeTypes)],
                    'status' => WorkOrderStatus::SELESAI,
                    'entry_date' => $entryDate,
                    'created_at' => $entryDate,
                    'updated_at' => $tSelesai,
                    'total_transaksi' => 175000 + ($i * 10000),
                ]
            );

            // Attach single FT service
            WorkOrderService::where('work_order_id', $order->id)->delete();
            WorkOrderService::create([
                'work_order_id' => $order->id,
                'service_id' => $ftService->id,
                'cost' => $ftService->price,
            ]);

            DB::table('work_orders')->where('id', $order->id)->update(['fast_track_status' => 'yes']);

            $order->logs()->delete();
            $steps = [
                ['step' => 'PREPARATION', 'time' => $tPrep],
                ['step' => 'SORTIR', 'time' => $tSortir],
                ['step' => 'PRODUCTION', 'time' => $tProd],
                ['step' => 'QC', 'time' => $tQC],
                ['step' => 'SELESAI', 'time' => $tSelesai],
            ];

            foreach ($steps as $s) {
                WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'action' => 'STATUS_CHANGE',
                    'step' => $s['step'],
                    'description' => 'Status berubah ke ' . $s['step'],
                    'created_at' => $s['time'],
                    'updated_at' => $s['time'],
                ]);
            }
        }

        // C. 10 SPK: GAGAL OPERASIONAL (Kendala Teknis, CX FollowUp)
        for ($i = 1; $i <= 10; $i++) {
            $entryDate = Carbon::parse('2026-09-05 10:00:00')->addDays($i);
            $spkNumber = sprintf('S-2609-%02d-%04d-OPS', $entryDate->day, 3000 + $i);

            $order = WorkOrder::updateOrCreate(
                ['spk_number' => $spkNumber],
                [
                    'customer_name' => 'Pelanggan FT Kendala ' . $i,
                    'customer_phone' => '081234567890',
                    'shoe_brand' => $shoeBrands[$i % count($shoeBrands)],
                    'shoe_type' => $shoeTypes[$i % count($shoeTypes)],
                    'status' => WorkOrderStatus::PRODUCTION,
                    'entry_date' => $entryDate,
                    'created_at' => $entryDate,
                    'updated_at' => $entryDate->copy()->addDays(2),
                    'total_transaksi' => 200000 + ($i * 20000),
                ]
            );

            // Attach single FT service
            WorkOrderService::where('work_order_id', $order->id)->delete();
            WorkOrderService::create([
                'work_order_id' => $order->id,
                'service_id' => $ftService->id,
                'cost' => $ftService->price,
            ]);

            DB::table('work_orders')->where('id', $order->id)->update(['fast_track_status' => 'yes']);

            $order->logs()->delete();
            WorkOrderLog::create([
                'work_order_id' => $order->id,
                'action' => 'STATUS_CHANGE',
                'step' => 'PRODUCTION',
                'description' => 'Menunggu material khusus / kendala operasional',
                'created_at' => $entryDate->copy()->addDay(),
                'updated_at' => $entryDate->copy()->addDay(),
            ]);

            WorkOrderLog::create([
                'work_order_id' => $order->id,
                'action' => 'KENDALA_OPERASIONAL',
                'step' => 'PRODUCTION',
                'description' => 'Kendala teknis sol tidak cocok dan perlu restock material.',
                'created_at' => $entryDate->copy()->addDays(2),
                'updated_at' => $entryDate->copy()->addDays(2),
            ]);
        }

        // D. 15 SPK: PENDING CS (Status SPK_PENDING, Fast Track)
        for ($i = 1; $i <= 15; $i++) {
            $createdAt = Carbon::parse('2026-09-10 11:00:00')->addDays($i % 12);
            $spkNumber = sprintf('S-2609-%02d-%04d-PND', $createdAt->day, 4000 + $i);

            $order = WorkOrder::updateOrCreate(
                ['spk_number' => $spkNumber],
                [
                    'customer_name' => 'Pelanggan FT Pending ' . $i,
                    'customer_phone' => '081234567890',
                    'shoe_brand' => $shoeBrands[$i % count($shoeBrands)],
                    'shoe_type' => $shoeTypes[$i % count($shoeTypes)],
                    'status' => WorkOrderStatus::SPK_PENDING,
                    'entry_date' => $createdAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'total_transaksi' => 220000 + ($i * 10000),
                ]
            );

            WorkOrderService::where('work_order_id', $order->id)->delete();
            WorkOrderService::create([
                'work_order_id' => $order->id,
                'service_id' => $ftService->id,
                'cost' => $ftService->price,
            ]);

            DB::table('work_orders')->where('id', $order->id)->update(['fast_track_status' => 'yes']);
        }

        // E. 37 SPK: BATAL FAST TRACK (Tepat 37 SPK seperti kasus produksi pengguna!)
        // 17 SPK berstatus SELESAI, 20 SPK berstatus lainnya
        for ($i = 1; $i <= 37; $i++) {
            $entryDate = Carbon::parse('2026-09-04 10:00:00')->addDays($i % 17);
            $spkNumber = sprintf('S-2609-%02d-%04d-BATAL', $entryDate->day, 5000 + $i);

            // 17 SPK berstatus SELESAI (persis seperti angka 17 di screenshot pengguna!)
            if ($i <= 17) {
                $status = WorkOrderStatus::SELESAI;
            } elseif ($i <= 22) {
                $status = WorkOrderStatus::PREPARATION;
            } elseif ($i <= 27) {
                $status = WorkOrderStatus::SORTIR;
            } elseif ($i <= 32) {
                $status = WorkOrderStatus::PRODUCTION;
            } else {
                $status = WorkOrderStatus::QC;
            }

            $order = WorkOrder::updateOrCreate(
                ['spk_number' => $spkNumber],
                [
                    'customer_name' => 'Pelanggan FT Batal ' . $i,
                    'customer_phone' => '081234567890',
                    'shoe_brand' => $shoeBrands[$i % count($shoeBrands)],
                    'shoe_type' => $shoeTypes[$i % count($shoeTypes)],
                    'status' => $status,
                    'fast_track_status' => 'no', // Diturunkan dari fast track
                    'entry_date' => $entryDate,
                    'created_at' => $entryDate,
                    'updated_at' => $entryDate->copy()->addDays(3),
                    'total_transaksi' => 250000 + ($i * 12000),
                ]
            );

            // Attach 2 services (FT + Reguler = diturunkan karena tambah jasa)
            WorkOrderService::where('work_order_id', $order->id)->delete();
            WorkOrderService::create([
                'work_order_id' => $order->id,
                'service_id' => $ftService->id,
                'cost' => $ftService->price,
            ]);
            WorkOrderService::create([
                'work_order_id' => $order->id,
                'service_id' => $regService->id,
                'cost' => $regService->price,
            ]);

            DB::table('work_orders')->where('id', $order->id)->update(['fast_track_status' => 'no']);

            $order->logs()->delete();

            // Tambahkan log downgrade fast track
            WorkOrderLog::create([
                'work_order_id' => $order->id,
                'action' => 'fast_track_downgrade',
                'step' => $status->value,
                'description' => 'SPK diturunkan dari Fast Track ke Biasa karena adanya penambahan jasa.',
                'created_at' => $entryDate->copy()->addDay(),
                'updated_at' => $entryDate->copy()->addDay(),
            ]);

            WorkOrderLog::create([
                'work_order_id' => $order->id,
                'action' => 'STATUS_CHANGE',
                'step' => $status->value,
                'description' => 'Status berubah ke ' . $status->value,
                'created_at' => $entryDate->copy()->addDays(2),
                'updated_at' => $entryDate->copy()->addDays(2),
            ]);
        }

        $this->command->info('Successfully seeded 102 Fast Track work orders for September 2026!');
    }
}
