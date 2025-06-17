<?php

use App\Custom\Enums\OrderStatus;
use App\Models\History;
use App\Models\Product;
use App\Models\Warehouse;

describe('orders', function () {
    test('user can get orders list', function () {
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

        $res = $this->json('GET', '/api/orders');

        expect($res)
            ->isOk()
            ->and($res)
            ->assertJsonStructure([
                'data' => [['id', 'customer', 'warehouse_id', 'status', 'completed_at', 'created_at', 'updated_at']],
            ]);
    });

    test('user can get single order', function () {
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
        $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();
        $res = $this->json('GET', "/api/orders/{$orderFromPost['data']['id']}");

        expect($res)
            ->isOk()
            ->and($res)
            ->assertJsonStructure([
                'data' => ['id', 'customer', 'warehouse_id', 'status', 'completed_at', 'created_at', 'updated_at'],
            ]);
    });

    describe('creating order', function () {
        test('user can create order', function () {
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
            $res = $this->json('POST', "/api/orders", $newOrder);

            expect($res)
                ->isOk()
                ->and($res)->assertJsonStructure([
                    'data' => ['id', 'customer', 'warehouse_id', 'created_at', 'updated_at', 'products'],
                ]);
        });

        test('count in stock changes', function () {
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

            expect($stockCountNew)->toBe($stockCountPrev - $productCount);
        });

        test('history record created', function () {
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
    });

    describe('updating order', function () {
        test('user can update order', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $updatedOrder = [
                'customer' => 'New Customer',
                'products' => [
                    [
                        'id' => $product->id,
                        'count' => $productCount + 1
                    ]
                ]
            ];
            $res = $this->json('PATCH', "/api/orders/{$orderFromPost['data']['id']}", $updatedOrder);

            expect($res)
                ->isOk()
                ->and($res)->assertJsonStructure([
                    'data' => ['id', 'customer', 'warehouse_id', 'status', 'completed_at', 'created_at', 'updated_at', 'products'],
                ]);
        });

        test('count in stock changes', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $newProductCount = $productCount + 1;
            $updatedOrder = [
                'customer' => 'New Customer',
                'products' => [
                    [
                        'id' => $product->id,
                        'count' => $newProductCount
                    ]
                ]
            ];

            $stockCountPrev = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $this->json('PATCH', "/api/orders/{$orderFromPost['data']['id']}", $updatedOrder);
            $stockCountNew = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;

            expect($stockCountNew)->toBe($stockCountPrev - 1);
        });

        test('history record created', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $newProductCount = $productCount + 1;
            $updatedOrder = [
                'customer' => 'New Customer',
                'products' => [
                    [
                        'id' => $product->id,
                        'count' => $newProductCount
                    ]
                ]
            ];

            $stockCountPrev = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $this->json('PATCH', "/api/orders/{$orderFromPost['data']['id']}", $updatedOrder);
            $stockCountNew = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $history = History::query()->orderByDesc('id')->first();

            expect($history->before)->toBe($stockCountPrev)
                ->and($history->after)->toBe($stockCountNew);
        });
    });

    describe('completing order', function () {
        test('user can complete order', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $postData = [
                'id' => $orderFromPost['data']['id'],
            ];
            $res = $this->json('POST', "api/orders/complete", $postData);

            expect($res)
                ->isOk()
                ->and($res)->assertJsonStructure([
                    'data' => ['id', 'customer', 'warehouse_id', 'status', 'completed_at', 'created_at', 'updated_at', 'products'],
                ])
                ->and($res->json()['data']['status'])->toBe(OrderStatus::COMPLETED->value);
        });
    });

    describe('cancelling order', function () {
        test('user can cancel order', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $postData = [
                'id' => $orderFromPost['data']['id'],
            ];
            $res = $this->json('POST', "api/orders/cancel", $postData);

            expect($res)
                ->isOk()
                ->and($res)->assertJsonStructure([
                    'data' => ['id', 'customer', 'warehouse_id', 'status', 'completed_at', 'created_at', 'updated_at', 'products'],
                ])
                ->and($res->json()['data']['status'])->toBe(OrderStatus::CANCELLED->value);
        });

        test('count in stock changes', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $postData = [
                'id' => $orderFromPost['data']['id'],
            ];

            $stockCountPrev = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $this->json('POST', "api/orders/cancel", $postData);
            $stockCountNew = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;

            expect($stockCountNew)->toBe($stockCountPrev + $productCount);
        });

        test('history record created', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $postData = [
                'id' => $orderFromPost['data']['id'],
            ];

            $stockCountPrev = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $this->json('POST', "api/orders/cancel", $postData);
            $stockCountNew = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $history = History::query()->orderByDesc('id')->first();

            expect($history->before)->toBe($stockCountPrev)
                ->and($history->after)->toBe($stockCountNew);
        });
    });

    describe('continuing order', function () {
        test('user can continue order', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $postData = [
                'id' => $orderFromPost['data']['id'],
            ];
            $this->json('POST', "api/orders/cancel", $postData);
            $res = $this->json('POST', "api/orders/continue", $postData);

            expect($res)
                ->isOk()
                ->and($res)->assertJsonStructure([
                    'data' => ['id', 'customer', 'warehouse_id', 'status', 'completed_at', 'created_at', 'updated_at', 'products'],
                ])
                ->and($res->json()['data']['status'])->toBe(OrderStatus::ACTIVE->value);
        });

        test('count in stock changes', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $postData = [
                'id' => $orderFromPost['data']['id'],
            ];

            $this->json('POST', "api/orders/cancel", $postData);

            $stockCountPrev = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $this->json('POST', "api/orders/continue", $postData);
            $stockCountNew = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;

            expect($stockCountNew)->toBe($stockCountPrev - $productCount);
        });

        test('history record created', function () {
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
            $orderFromPost = $this->json('POST', "/api/orders", $newOrder)->json();

            $postData = [
                'id' => $orderFromPost['data']['id'],
            ];

            $this->json('POST', "api/orders/cancel", $postData);

            $stockCountPrev = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $this->json('POST', "api/orders/continue", $postData);
            $stockCountNew = $product->stocks()->firstWhere('warehouse_id', $warehouseId)->stock;
            $history = History::query()->orderByDesc('id')->first();

            expect($history->before)->toBe($stockCountPrev)
                ->and($history->after)->toBe($stockCountNew);
        });
    });
})->group('orders');
