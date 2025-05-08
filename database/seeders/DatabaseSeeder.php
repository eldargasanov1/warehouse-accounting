<?php

namespace Database\Seeders;

use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Warehouse::factory(5)->create();
        Product::factory(50)->create();
        $this->call(StockSeeder::class);
        $this->call(OrderSeeder::class);
    }
}
