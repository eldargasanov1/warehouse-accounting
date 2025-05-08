<?php

namespace App\Listeners;

use App\Custom\Enums\OrderStatus;
use App\Events\OrderCancelled;
use App\Events\OrderContinued;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;

class ChangeProductsCountInStock
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated|OrderUpdated|OrderCancelled|OrderContinued $event): void
    {
        /*
         * Выполняется в случае создания заказа.
         * Для каждого товара уменьшает количество на складе на количество единиц в заказе.
         * */
        if ($event instanceof OrderCreated) {
            foreach ($event->order->products as $product) {
                $stock = $product->stocks->firstWhere('warehouse_id', $event->order->warehouse_id);
                $stock->stock = $stock->stock - $product->pivot->count;
                $stock->save();
            }
        }

        /*
         * Выполняется в случае обновления заказа.
         * Действие состоит из следующих шагов:
         * 0. Из новой и старой версий продуктов создаются новые коллекции,
         * которые содержат: общие элементы, элементы только в предыдущем заказе
         * и элементы только в новом заказе;
         * 1. Для общих элементов проверяется, изменилось ли количество. Если изменилось,
         * данные в таблице stocks обновляются;
         * 2. Для элементов только из предыдущей версии количество товаров
         * в таблице stocks увеличивается;
         * 3.  Для элементов только из новой версии количество товаров
         * в таблице stocks увеличивается.
         * */
        if ($event instanceof OrderUpdated) {
            if ($event->newOrder->status == OrderStatus::COMPLETED->value) {
                return;
            }
            $oldOrderProducts = $event->oldOrder->products;
            $newOrderProducts = $event->newOrder->products;

            $intersect = $oldOrderProducts->intersect($newOrderProducts);
            $oldProducts = $oldOrderProducts->diff($newOrderProducts);
            $newProducts = $newOrderProducts->diff($oldOrderProducts);

            foreach ($intersect as $product) {
                $oldCount = $oldOrderProducts->find($product->id)->pivot->count;
                $newCount = $newOrderProducts->find($product->id)->pivot->count;
                if ($oldCount !== $newCount) {
                    $productStock = $product->stocks->firstWhere('warehouse_id', $event->oldOrder->warehouse_id);
                    $productStock->update([
                        'stock' => $productStock->stock - ($newCount - $oldCount),
                    ]);
                }
            }
            foreach ($oldProducts as $product) {
                $productStock = $product->stocks->firstWhere('warehouse_id', $event->oldOrder->warehouse_id);
                $count = $oldOrderProducts->find($product->id)->pivot->count;
                $productStock->update([
                    'stock' => $productStock->stock + $count,
                ]);
            }
            foreach ($newProducts as $product) {
                $productStock = $product->stocks->firstWhere('warehouse_id', $event->newOrder->warehouse_id);
                $count = $newOrderProducts->find($product->id)->pivot->count;
                $productStock->update([
                    'stock' => $productStock->stock - $count,
                ]);
            }
        }

        /*
         * Выполняется в случае отмены заказа.
         * Для каждого товара увеличивает количество на складе на количество единиц в заказе.
         * */
        if ($event instanceof OrderCancelled) {
            foreach ($event->order->products as $product) {
                $stock = $product->stocks->firstWhere('warehouse_id', $event->order->warehouse_id);
                $stock->stock = $stock->stock + $product->pivot->count;
                $stock->save();
            }
        }

        /*
         * Выполняется в случае возобновления заказа.
         * Для каждого товара уменьшает количество на складе на количество единиц в заказе.
         * */
        if ($event instanceof OrderContinued) {
            foreach ($event->order->products as $product) {
                $stock = $product->stocks->firstWhere('warehouse_id', $event->order->warehouse_id);
                $stock->stock = $stock->stock - $product->pivot->count;
                $stock->save();
            }
        }
    }
}
