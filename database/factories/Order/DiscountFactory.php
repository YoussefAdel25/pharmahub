<?php

namespace Database\Factories\Order;

use App\Models\Product\Product;
use App\Models\Supplier\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'supplier_id' => Supplier::factory(),
            'product_id' => Product::factory(),
            'discount_type' => $this->faker->randomElement(['percentage','fixed']),
            'value' => $this->faker->numberBetween(5, 30),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ];
    }
}
