<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                if (rand(0, 1)) {
                    continue;
                }

                Stock::factory()->for($warehouse)->for($product)->create();
            }
        }
    }
}
