<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $warehouses = Warehouse::factory(5)->create();
        $products = Product::factory(50)->create();
        foreach ($warehouses as $warehouse) {
            foreach ($products as $product) {
                if (rand(0, 5)) {
                    Stock::factory()->for($warehouse)->for($product)->create();
                }  else {
                    Stock::factory()->for($warehouse)->for($product)->create([
                        'stock' => 0
                    ]);
                }
            }
        }

        for ($i = 1; $i <= 5; $i++) {
            $productsIdForOrder = Arr::random($products->pluck('id')->all(), rand(1, 7));
            $productsForOrder = $products->whereIn('id', $productsIdForOrder);

            foreach ($warehouses as $warehouse) {
                foreach ($productsForOrder as $product) {
                    $countInStock = $product->stocks->firstWhere('warehouse_id', $warehouse->id)->stock;
                    if ($countInStock == 0) {
                        continue;
                    }

                    $countOfProduct = rand(1, 22);
                    if ($countInStock < $countOfProduct) {
                        continue;
                    }

                    Order::factory()->for($warehouse)->hasAttached($product, [
                        'count' => $countOfProduct,
                        'created_at' => now(),
                        'updated_at' => now()
                    ])->create();
                }
            }
        }
    }
}
