<?php

namespace Database\Seeders;

use App\Models\Region\Region;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $regions = [
            ['name' => 'Cairo', 'latitude' => 30.0444, 'longitude' => 31.2357],
            ['name' => 'Giza', 'latitude' => 30.0131, 'longitude' => 31.2089],
            ['name' => 'Alexandria', 'latitude' => 31.2001, 'longitude' => 29.9187],
            ['name' => 'Aswan', 'latitude' => 24.0889, 'longitude' => 32.8998],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
