<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\Service;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Promo Percentage - Resize 20% Off
        $resizePromo = Promotion::create([
            'code' => 'RESIZE20',
            'name' => 'Diskon 20% untuk Resize',
            'description' => 'Dapatkan diskon 20% untuk layanan Resize',
            'type' => Promotion::TYPE_PERCENTAGE,
            'discount_percentage' => 20.00,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(3),
            'is_active' => true,
            'applicable_to' => Promotion::APPLICABLE_SPECIFIC_SERVICES,
            'customer_tier' => Promotion::TIER_ALL,
            'max_usage_total' => 100,
            'max_usage_per_customer' => 3,
            'is_stackable' => false,
            'priority' => 5,
        ]);

        // Attach to Resize service (assuming service ID 1 is Resize)
        $resizeService = Service::where('name', 'LIKE', '%Resize%')->first();
        if ($resizeService) {
            $resizePromo->services()->attach($resizeService->id);
        }

        // 2. Promo Fixed Amount - Hemat 50K
        Promotion::create([
            'code' => 'HEMAT50K',
            'name' => 'Hemat Rp 50.000',
            'description' => 'Potongan langsung Rp 50.000 untuk semua layanan',
            'type' => Promotion::TYPE_FIXED,
            'discount_amount' => 50000,
            'min_purchase_amount' => 200000,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(2),
            'is_active' => true,
            'applicable_to' => Promotion::APPLICABLE_ALL_SERVICES,
            'customer_tier' => Promotion::TIER_ALL,
            'max_usage_total' => 50,
            'max_usage_per_customer' => 1,
            'is_stackable' => false,
            'priority' => 3,
        ]);

        // 3. Promo VIP - 30% Off
        Promotion::create([
            'code' => 'VIP30',
            'name' => 'VIP Exclusive 30%',
            'description' => 'Diskon khusus 30% untuk customer VIP',
            'type' => Promotion::TYPE_PERCENTAGE,
            'discount_percentage' => 30.00,
            'max_discount_amount' => 500000,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(6),
            'is_active' => true,
            'applicable_to' => Promotion::APPLICABLE_ALL_SERVICES,
            'customer_tier' => Promotion::TIER_VIP,
            'max_usage_per_customer' => 5,
            'is_stackable' => false,
            'priority' => 10,
        ]);

        // 4. Promo Seasonal - Ramadan 15%
        Promotion::create([
            'code' => 'RAMADAN2026',
            'name' => 'Promo Ramadan 15%',
            'description' => 'Diskon spesial Ramadan untuk semua layanan',
            'type' => Promotion::TYPE_PERCENTAGE,
            'discount_percentage' => 15.00,
            'valid_from' => Carbon::parse('2026-03-01'),
            'valid_until' => Carbon::parse('2026-04-30'),
            'is_active' => true,
            'applicable_to' => Promotion::APPLICABLE_ALL_SERVICES,
            'customer_tier' => Promotion::TIER_ALL,
            'is_stackable' => true,
            'priority' => 7,
        ]);

        // 5. Promo Bundle - Resize + Repaint
        $bundlePromo = Promotion::create([
            'code' => 'BUNDLE25',
            'name' => 'Bundle Resize + Repaint 25%',
            'description' => 'Diskon 25% jika mengambil Resize dan Repaint bersamaan',
            'type' => Promotion::TYPE_BUNDLE,
            'discount_percentage' => 25.00,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(3),
            'is_active' => true,
            'applicable_to' => Promotion::APPLICABLE_SPECIFIC_SERVICES,
            'customer_tier' => Promotion::TIER_ALL,
            'max_usage_per_customer' => 2,
            'is_stackable' => false,
            'priority' => 8,
        ]);

        // Attach bundle services
        $resizeService = Service::where('name', 'LIKE', '%Resize%')->first();
        $repaintService = Service::where('name', 'LIKE', '%Repaint%')->first();
        
        if ($resizeService && $repaintService) {
            $bundlePromo->services()->attach([$resizeService->id, $repaintService->id]);
            
            // Create bundle requirement
            $bundlePromo->bundles()->create([
                'required_services' => [$resizeService->id, $repaintService->id],
            ]);
        }

        // 6. Promo New Customer
        Promotion::create([
            'code' => 'WELCOME10',
            'name' => 'Welcome Bonus 10%',
            'description' => 'Diskon 10% untuk customer baru',
            'type' => Promotion::TYPE_PERCENTAGE,
            'discount_percentage' => 10.00,
            'max_discount_amount' => 100000,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addYear(),
            'is_active' => true,
            'applicable_to' => Promotion::APPLICABLE_ALL_SERVICES,
            'customer_tier' => Promotion::TIER_NEW,
            'max_usage_per_customer' => 1,
            'is_stackable' => false,
            'priority' => 6,
        ]);

        $this->command->info('✅ Sample promotions created successfully!');
    }
}
