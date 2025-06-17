<?php

use App\Models\History;
use App\Models\Product;
use App\Models\Warehouse;

describe('histories', function () {
    test('history record can be created', function () {
        $warehouseId = Warehouse::query()->first()->id;
        $productCount = 1;
        $product = Product::query()
            ->whereRelation('stocks', 'warehouse_id', $warehouseId)
            ->whereRelation('stocks', 'stock', '>', $productCount)
            ->first();
        $newOrder = [
            'customer' => 'customer',
            'warehouse_id' => $warehouseId,
            'products' => [
                [
                    'id' => $product->id,
                    'count' => $productCount,
                ]
            ]
        ];
        $stockCountPrev = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
        $this->json('POST', "/api/orders", $newOrder);
        $stockCountNew = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
        $history = History::query()->first();

        expect($history->before)->toBe($stockCountPrev)
            ->and($history->after)->toBe($stockCountNew);
    });

    test('user can get histories list', function () {
        $warehouseId = Warehouse::query()->first()->id;
        $productCount = 1;
        $product = Product::query()
            ->whereRelation('stocks', 'warehouse_id', $warehouseId)
            ->whereRelation('stocks', 'stock', '>', $productCount)
            ->first();
        $newOrder = [
            'customer' => 'customer',
            'warehouse_id' => $warehouseId,
            'products' => [
                [
                    'id' => $product->id,
                    'count' => $productCount,
                ]
            ]
        ];
        $this->json('POST', "/api/orders", $newOrder);

        $res = $this->json('GET', '/api/histories');
        expect($res)
            ->isOk()
            ->and($res)
            ->assertJsonStructure([
                'data' => [['id', 'before', 'after', 'created_at', 'stock']],
            ]);
    });
})->group('histories');
