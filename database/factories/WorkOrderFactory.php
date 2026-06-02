<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition()
    {
        return [
            'spk_number' => 'S-' . $this->faker->unique()->numerify('####'),
            'customer_name' => $this->faker->name,
            'customer_phone' => '08' . $this->faker->numerify('##########'),
            'shoe_brand' => 'Nike',
            'shoe_type' => 'Sneakers',
            'status' => WorkOrderStatus::SPK_PENDING,
            'waktu' => now(),
            'total_transaksi' => 0,
            'total_paid' => 0,
            'sisa_tagihan' => 0,
        ];
    }
}
