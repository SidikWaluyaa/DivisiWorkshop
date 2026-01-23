<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AlgorithmConfig;

class AlgorithmConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $algorithms = [
            [
                'algorithm_name' => 'auto_assignment',
                'is_active' => false,
                'description' => 'Automatically assign orders to technicians based on workload, skill, and performance',
                'parameters' => [
                    'max_concurrent_orders' => 5,
                    'skill_matching_weight' => 60,
                    'performance_weight' => 30,
                    'workload_weight' => 10,
                    'auto_assign_production' => true,
                    'auto_assign_qc' => false,
                ],
                'status' => 'idle',
            ],
            [
                'algorithm_name' => 'load_balancing',
                'is_active' => true,
                'description' => 'Monitor and balance workload across workshop stations',
                'parameters' => [
                    'bottleneck_threshold' => 15,
                    'warning_threshold' => 10,
                    'enable_alerts' => true,
                    'enable_recommendations' => true,
                ],
                'status' => 'idle',
            ],
            [
                'algorithm_name' => 'priority_calculation',
                'is_active' => true,
                'description' => 'Calculate dynamic priority scores for orders based on multiple factors',
                'parameters' => [
                    'deadline_weight' => 50,
                    'value_weight' => 20,
                    'customer_tier_weight' => 20,
                    'revision_bonus' => 10,
                    'vip_multiplier' => 1.5,
                ],
                'status' => 'idle',
            ],
        ];

        foreach ($algorithms as $algorithm) {
            AlgorithmConfig::updateOrCreate(
                ['algorithm_name' => $algorithm['algorithm_name']],
                $algorithm
            );
        }

        $this->command->info('Algorithm configurations seeded successfully!');
    }
}
