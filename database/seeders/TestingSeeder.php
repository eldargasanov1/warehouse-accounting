<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehousesCount = 2;
        $productsCount = 2;

        $warehouses = Warehouse::factory($warehousesCount)->create();
        $products = Product::factory($productsCount)->create();
        foreach ($warehouses as $warehouse) {
            $i = 1;
            foreach ($products as $product) {
                if ($i <= $productsCount / 2) {
                    Stock::factory()->for($warehouse)->for($product)->create();
                }  else {
                    Stock::factory()->for($warehouse)->for($product)->create([
                        'stock' => 0
                    ]);
                }
            }
        }
    }
}
