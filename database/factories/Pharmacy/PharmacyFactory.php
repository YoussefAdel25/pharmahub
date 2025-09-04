<?php

namespace Database\Factories\Pharmacy;

use App\Models\User;
use App\Models\Region\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PharmacyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->state(['role' => 'pharmacy']),
            'pharmacy_name' => $this->faker->company . ' Pharmacy',
            'owner_name' => $this->faker->name,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'region_id' => Region::factory(),
        ];
    }
}
