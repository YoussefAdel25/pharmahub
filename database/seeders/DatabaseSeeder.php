<?php

namespace Database\Seeders;

use App\Models\Order\Order;
use App\Models\Region\Region;
use App\Models\Order\Discount;
use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use App\Models\Pharmacy\Pharmacy;
use App\Models\Supplier\Supplier;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        Region::factory(5)->create();

        Supplier::factory(5)
            ->has(Product::factory(10))
            ->create();

        Pharmacy::factory(10)
            ->has(
                Order::factory(3)->has(OrderItem::factory(5))
            )
            ->create();

        SupplierDiscount::factory(10)->create();
    }
}
