<?php

namespace Database\Factories\Order;

use App\Models\Pharmacy\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'pharmacy_id' => Pharmacy::factory(),
            'status' => $this->faker->randomElement(['pending','delivered','cancelled','partial']),
            'total_price' => 0,
        ];
    }
}
