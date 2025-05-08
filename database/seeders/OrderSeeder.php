<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        foreach ($warehouses as $warehouse) {
            foreach ($products as $product) {
                $productInThisWarehouse = !empty($product->stocks->firstWhere('warehouse_id', $warehouse->id));
                if (!$productInThisWarehouse) {
                    continue;
                }
                Order::factory(3)->for($warehouse)->hasAttached($product, ['count' => rand(1, 3)])->create();
            }
        }
    }
}
