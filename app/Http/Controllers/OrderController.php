<?php

namespace App\Http\Controllers;

use App\Custom\Enums\OrderStatus;
use App\Events\OrderCancelled;
use App\Events\OrderContinued;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\CompleteOrderRequest;
use App\Http\Requests\ContinueOrderRequest;
use App\Http\Requests\GetOrdersRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Throwable;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws Throwable
     */
    public function index(GetOrdersRequest $request): ResourceCollection
    {
        $filter = $request->safe()->only('filter');
        $pagination = $request->safe()->only('pagination');

        $result = [];
        if (!empty($filter) && !empty($pagination)) {
            $filter = $filter['filter'];
            $pagination = $pagination['pagination'];
            $result = Order::filterOrders($filter)->simplePaginate(perPage: $pagination['perPage'], page: $pagination['page']);
        } elseif (!empty($filter)) {
            $filter = $filter['filter'];
            $result = Order::filterOrders($filter)->get();
        } elseif (!empty($pagination)) {
            $pagination = $pagination['pagination'];
            $result = Order::query()->simplePaginate(perPage: $pagination['perPage'], page: $pagination['page']);
        } else {
            $result = Order::all();
        }
        return OrderResource::collection($result);
    }

    /**
     * Display the resource.
     * @throws Throwable
     */
    public function one(Request $request, Order $order): JsonResource
    {
        return $order->toResource();
    }

    /**
     * Store a newly created resource in storage.
     * @throws Throwable
     */
    public function store(StoreOrderRequest $request): JsonResource
    {
        $validOrder = $request->safe()->except('products');
        $validProducts = $request->safe()->only('products')['products'];

        $order = Order::query()->create($validOrder);
        foreach ($validProducts as $product) {
            $order->products()->attach($product['id'], ['count' => $product['count']]);
        }
        OrderCreated::dispatch($order);

        return $order->load('products')->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @throws Throwable
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResource
    {
        $validOrder = $request->safe()->except('products');
        $validProducts = $request->safe()->only('products');

        $oldOrder = $order->load('products')->replicate();
        $newOrder = $order->load('products');
        if (!empty($validOrder)) {
            $newOrder->update(['customer' => $validOrder['customer']]);
        }
        if (!empty($validProducts)) {
            $newProducts = [];
            foreach ($validProducts['products'] as $product) {
                $newProducts[$product['id']] = ['count' => $product['count']];
            }
            $newOrder->products()->sync($newProducts);
        }
        $newOrder->refresh();
        OrderUpdated::dispatch($oldOrder, $newOrder);

        return $newOrder->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    /**
     * Complete the specified order
     * @throws Throwable
     */
    public function complete(CompleteOrderRequest $request): JsonResource
    {
        $id = $request->safe()->only('id')['id'];
        $order = Order::query()->find($id);
        $order->update([
            'status' => OrderStatus::COMPLETED,
            'completed_at' => now()
        ]);
        return $order->load('products')->toResource();
    }

    /**
     * Cancel the specified active order
     * @throws Throwable
     */
    public function cancel(CancelOrderRequest $request): JsonResource
    {
        $id = $request->safe()->only('id')['id'];
        $order = Order::query()->find($id);
        $order->update(['status' => OrderStatus::CANCELLED]);
        OrderCancelled::dispatch($order);
        return $order->load('products')->toResource();
    }

    /**
     * Continue the specified cancelled order
     * @throws Throwable
     */
    public function continue(ContinueOrderRequest $request): JsonResource
    {
        $id = $request->safe()->only('id')['id'];
        $order = Order::query()->find($id);
        $order->update(['status' => OrderStatus::ACTIVE]);
        OrderContinued::dispatch($order);
        return $order->load('products')->toResource();
    }
}
