<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CsLead;
use App\Models\CsActivity;
use App\Models\CsQuotation;
use App\Models\CsSpk;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CsSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Ensure CS Users exist
        $csUsers = User::whereJsonContains('access_rights', 'cs')->get();
        if ($csUsers->isEmpty()) {
            $csUser = User::create([
                'name' => 'CS Demo',
                'email' => 'cs@example.com',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'access_rights' => ['cs'],
                'cs_code' => 'CS1',
            ]);
            $csUsers->push($csUser);
            $this->command->info('Created demo CS user (cs@example.com)');
        }

        $services = Service::inRandomOrder()->limit(5)->get();
        if ($services->isEmpty()) {
            $this->command->error('No services found! Please run ServiceSeeder first.');
            return;
        }

        $this->command->info('Seeding CS System Data...');

        // 2. Create GREETING Leads (New + Invest)
        for ($i = 0; $i < 10; $i++) {
            $status = $faker->randomElement([CsLead::STATUS_GREETING, CsLead::STATUS_GREETING]); // Weight towards Greeting
            $priority = $faker->randomElement(['HOT', 'WARM', 'COLD']);
            $source = $faker->randomElement(['WhatsApp', 'Instagram', 'Website']);
            
            $lead = CsLead::create([
                'customer_name' => $faker->name,
                'customer_phone' => $faker->phoneNumber,
                'customer_email' => $faker->safeEmail,
                'customer_address' => $faker->address,
                'customer_city' => $faker->city,
                'customer_province' => 'Jawa Barat', // Simplify
                'source' => $source,
                'priority' => $priority,
                'status' => $status,
                'cs_id' => $csUsers->random()->id,
                'first_contact_at' => Carbon::now()->subDays(rand(1, 5)),
                'last_activity_at' => Carbon::now()->subHours(rand(1, 24)),
                'first_response_at' => Carbon::now()->subDays(rand(1, 5))->addMinutes(rand(5, 60)),
                'response_time_minutes' => rand(5, 60),
            ]);

            // Add Activity Logs
            $lead->activities()->create([
                'user_id' => $lead->cs_id,
                'type' => 'CHAT',
                'channel' => $source,
                'content' => 'Customer bertanya tentang layanan repaint.',
                'created_at' => $lead->first_contact_at,
            ]);
        }

        // 3. Create KONSULTASI Leads (Draft Quotation, Sent Quotation)
        for ($i = 0; $i < 8; $i++) {
            $lead = CsLead::create([
                'customer_name' => $faker->name,
                'customer_phone' => $faker->phoneNumber,
                'source' => 'WhatsApp',
                'priority' => 'HOT',
                'status' => CsLead::STATUS_KONSULTASI,
                'cs_id' => $csUsers->random()->id,
                'first_contact_at' => Carbon::now()->subDays(rand(3, 10)),
                'last_activity_at' => Carbon::now()->subHours(rand(1, 48)),
            ]);

            // Create Quotation
            $items = [];
            $total = 0;
            $randomServices = $services->random(rand(1, 3));
            foreach ($randomServices as $svc) {
                $qty = rand(1, 2);
                $price = $svc->price;
                $items[] = [
                    'service_name' => $svc->name,
                    'price' => $price,
                    'qty' => $qty,
                    'description' => 'Dummy service item'
                ];
                $total += ($price * $qty);
            }

            $quotationStatus = $faker->randomElement([CsQuotation::STATUS_DRAFT, CsQuotation::STATUS_SENT]);
            
            $quotation = $lead->quotations()->create([
                'quotation_number' => CsQuotation::generateQuotationNumber(),
                'version' => 1,
                'items' => $items,
                'discount' => 0,
                'discount_type' => 'AMOUNT',
                'total' => $total,
                'status' => $quotationStatus,
                'valid_until' => Carbon::now()->addDays(7),
            ]);
            
            $lead->activities()->create([
                'user_id' => $lead->cs_id,
                'type' => 'NOTE',
                'content' => "Quotation #{$quotation->quotation_number} generated.",
            ]);
        }

        // 4. Create CLOSING Leads (Accepted Quotation -> Waiting SPK -> SPK Created)
        for ($i = 0; $i < 5; $i++) {
            $lead = CsLead::create([
                'customer_name' => $faker->name,
                'customer_phone' => $faker->phoneNumber,
                'source' => 'Instagram',
                'priority' => 'HOT',
                'status' => CsLead::STATUS_CLOSING,
                'cs_id' => $csUsers->random()->id,
                'first_contact_at' => Carbon::now()->subDays(rand(5, 15)),
                'last_activity_at' => Carbon::now(),
            ]);

            // Create Accepted Quotation
            $items = [];
            $total = 0;
            $randomServices = $services->random(rand(2, 4));
            foreach ($randomServices as $svc) {
                $items[] = [
                    'service_name' => $svc->name,
                    'price' => $svc->price,
                    'qty' => 1,
                    'id' => $svc->id // Important for SPK
                ];
                $total += $svc->price;
            }

            $quotation = $lead->quotations()->create([
                'quotation_number' => CsQuotation::generateQuotationNumber(),
                'version' => 1,
                'items' => $items,
                'total' => $total,
                'status' => CsQuotation::STATUS_ACCEPTED,
                'valid_until' => Carbon::now()->addDays(7),
            ]);

            $lead->update(['expected_value' => $total]);

            // Randomly create SPK for some
            if ($faker->boolean(70)) {
                // Ensure customer exists
                $customer = Customer::create([
                    'name' => $lead->customer_name,
                    'phone' => \App\Helpers\PhoneHelper::normalize($lead->customer_phone),
                    'address' => $faker->address,
                    'city' => $faker->city,
                    'province' => 'Jawa Barat',
                ]);

                // Create SPK
                $deliveryType = $faker->randomElement(['Offline', 'Online', 'Pickup']);
                $spkNumber = CsSpk::generateSpkNumber($deliveryType, $lead->cs->cs_code ?? 'SW');

                $spk = $lead->spk()->create([
                    'spk_number' => $spkNumber,
                    'customer_id' => $customer->id,
                    'services' => $items,
                    'total_price' => $total,
                    'dp_amount' => $total * 0.5,
                    'dp_status' => CsSpk::DP_PENDING,
                    'status' => CsSpk::STATUS_WAITING_DP,
                    'delivery_type' => $deliveryType,
                    'priority' => 'Reguler',
                    'shoe_size' => '42',
                    'expected_delivery_date' => Carbon::now()->addDays(7),
                ]);

                // Randomly mark DP as paid/verification
                if ($faker->boolean(50)) {
                    $spk->update([
                        'dp_status' => CsSpk::DP_PAID,
                        'status' => CsSpk::STATUS_DP_PAID,
                        'dp_paid_at' => Carbon::now(),
                        'payment_method' => 'Transfer Bank',
                    ]);
                } else {
                    // Always create at least one Waiting Verification if not paid
                    $spk->update([
                        'status' => CsSpk::STATUS_WAITING_VERIFICATION,
                        'payment_method' => 'Transfer Bank',
                        'proof_image' => 'dummy_proof.jpg'
                    ]);
                }
            }
        }
        
        $this->command->info('CS System Seeder completed successfully!');
    }
}
