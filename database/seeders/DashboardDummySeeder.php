<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Material;
use App\Models\Service;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\Purchase;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Technicians exist
        $techs = User::where('role', 'technician')->get();
        if ($techs->isEmpty()) {
            $tech1 = User::create([
                'name' => 'Budi Technician',
                'email' => 'tech1@example.com',
                'password' => bcrypt('password'),
                'role' => 'technician',
            ]);
            $tech2 = User::create([
                'name' => 'Andi Sol',
                'email' => 'tech2@example.com',
                'password' => bcrypt('password'),
                'role' => 'technician',
            ]);
            $tech3 = User::create([
                'name' => 'Citra Upper',
                'email' => 'tech3@example.com',
                'password' => bcrypt('password'),
                'role' => 'technician',
            ]);
            $techs = collect([$tech1, $tech2, $tech3]);
        }

        // 2. Update Materials with Prices
        $substances = [
            ['name' => 'Leather Cleaner', 'price' => 150000],
            ['name' => 'Suede Brush', 'price' => 50000],
            ['name' => 'Midsole Paint', 'price' => 85000],
            ['name' => 'Premium Glue', 'price' => 75000],
            ['name' => 'Shoe Lace Black', 'price' => 25000],
        ];

        foreach ($substances as $item) {
            Material::firstOrCreate(
                ['name' => $item['name']],
                [
                    'sku' => strtoupper(substr($item['name'], 0, 3)) . rand(100, 999),
                    'stock' => rand(5, 50),
                    'unit' => 'Pcs',
                    'min_stock' => 10,
                    'status' => 'Ready',
                    'price' => $item['price']
                ]
            );
        }
        $allMaterials = Material::all();

        // 3. Create Purchases (for Budget & Inventory Analytics)
        foreach($allMaterials as $mat) {
             Purchase::create([
                'po_number' => 'PO-' . date('Ymd') . '-' . rand(1000, 9999),
                'material_id' => $mat->id,
                'quantity' => rand(10, 50),
                'unit_price' => $mat->price * 0.8, // Beli lebih murah dikit
                'total_price' => ($mat->price * 0.8) * rand(10, 50),
                'status' => 'received',
                'payment_status' => 'paid',
                'created_by' => 1, // Admin
                'order_date' => Carbon::now()->subDays(rand(1, 14)),
                'received_date' => Carbon::now()->subDays(rand(0, 5)),
                'paid_amount' => ($mat->price * 0.8) * rand(10, 50),
             ]);
        }

        // 4. Generate Work Orders (Past 7 Days + Current)
        $statuses = WorkOrderStatus::cases();
        $services = Service::all();
        
        // Buat 30 order dlm 7 hari terakhir
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays(rand(0, 7));
            $status = $statuses[array_rand($statuses)];
            
            // Tentukan status logis agar chart distribusi bagus
            if ($i < 5) $status = WorkOrderStatus::DITERIMA;
            elseif ($i < 10) $status = WorkOrderStatus::PREPARATION;
            elseif ($i < 20) $status = WorkOrderStatus::PRODUCTION;
            else $status = WorkOrderStatus::SELESAI;

            $wo = WorkOrder::create([
                'spk_number' => 'SPK-' . date('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'customer_name' => 'Customer ' . $i,
                'customer_phone' => '0812345678' . $i,
                'status' => $status->value,
                'current_location' => $this->getLocation($status),
                'shoe_brand' => ['Nike', 'Adidas', 'New Balance', 'Convers'][rand(0, 3)],
                'shoe_type' => ['Sneakers', 'Running', 'Boots', 'Casual'][rand(0, 3)],
                'shoe_color' => ['Black', 'White', 'Red', 'Blue'][rand(0, 3)],
                'entry_date' => $date,
                'estimation_date' => $date->copy()->addDays(3),
                'created_at' => $date,
                'updated_at' => $date,
                // Assign Techs randomly for performance charts
                'technician_production_id' => $techs->random()->id,
                'pic_sortir_sol_id' => $techs->random()->id,
            ]);

            // Add Services
            if ($services->count() > 0) {
                $wo->services()->attach($services->random()->id, ['cost' => 100000]);
            }
            
            // Add Materials Used
            if ($allMaterials->count() > 0) {
                $wo->materials()->attach($allMaterials->random()->id, ['quantity' => rand(1, 3)]);
            }

            // Create Logs for Productivity Analytics (Preparation -> Sortir)
            // Log entry
            WorkOrderLog::create([
                'work_order_id' => $wo->id,
                'step' => 'DITERIMA',
                'action' => 'MOVED',
                'description' => 'Order Diterima',
                'created_at' => $date,
            ]);

            if ($status !== WorkOrderStatus::DITERIMA) {
                 // Log move to PREPARATION
                 $prepTime = $date->copy()->addHours(rand(1, 5));
                 WorkOrderLog::create([
                    'work_order_id' => $wo->id,
                    'step' => 'PREPARATION',
                    'action' => 'MOVED',
                    'description' => 'Masuk Preparation',
                    'created_at' => $prepTime,
                ]);

                // Log move to SORTIR (to calc prep time)
                if ($status !== WorkOrderStatus::PREPARATION) {
                    WorkOrderLog::create([
                        'work_order_id' => $wo->id,
                        'step' => 'SORTIR',
                        'action' => 'MOVED',
                        'description' => 'Selesai Prep, masuk Sortir',
                        'created_at' => $prepTime->copy()->addHours(rand(1, 4)), // Durasi pengerjaan 1-4 jam
                    ]);
                }
            }
        }
    }

    private function getLocation($status)
    {
        return match($status) {
            WorkOrderStatus::DITERIMA => 'Gudang Penerimaan',
            WorkOrderStatus::ASSESSMENT => 'Rak Sepatu',
            WorkOrderStatus::PREPARATION => 'Rumah Hijau',
            WorkOrderStatus::SORTIR => 'Rumah Hijau',
            WorkOrderStatus::PRODUCTION => 'Rumah Abu',
            WorkOrderStatus::QC => 'Rumah Abu',
            WorkOrderStatus::SELESAI => 'Rak Selesai / Pickup Area (Rumah Hijau)',
            default => 'Unknown',
        };
    }
}
