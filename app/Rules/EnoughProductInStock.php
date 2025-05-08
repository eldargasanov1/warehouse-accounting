<?php

namespace App\Rules;

use App\Models\Order;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Thе rule checks whether there are enough items in the stock.
 */
class EnoughProductInStock implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /*
         * Вариант выполняемых действий выбирается на основе полного названия метода контроллера,
         * обрабатывающего текущий запрос.
         *
         * Каждый case выполняет следующий набор действий:
         * 1. Проверка, существует ли продукт;
         * 2. Проверка есть ли продукт в запасе;
         * 3. Проверка, больше ли количество товаров в запасе, чем количество товаров в заказе.
         *
         * В случае провала проверки возвращается сообщение об ошибке.
         * */
        $currentControllerMethodName = request()->route()->getActionName();
        switch ($currentControllerMethodName) {
            case 'App\Http\Controllers\OrderController@store':
                $product = Product::query()->find($value['id']);
                if (!isset($product)) {
                    $fail('Product not found');
                    break;
                }
                $warehouse_id = $this->data['warehouse_id'];
                $productStock = $product->stocks()->firstWhere('warehouse_id', $warehouse_id);
                if (empty($productStock)) {
                    $fail('Product not in this stock');
                    break;
                }
                if ($productStock->stock < $value['count']) {
                    $fail('Quantity out of stock');
                    break;
                }
                break;

            case 'App\Http\Controllers\OrderController@update':
                $order = request()->route('order');
                $product = Product::query()->find($value['id']);
                $orderProduct = $order->products()->find($value['id']);
                if (!isset($product)) {
                    $fail('Product not found');
                    break;
                }
                $warehouse_id = $order->warehouse_id;
                $productStock = $product->stocks()->firstWhere('warehouse_id', $warehouse_id);
                $productCount = !empty($orderProduct) ? $value['count'] - $orderProduct->pivot->count : $value['count'];
                if (empty($productStock)) {
                    $fail('Product not in this stock');
                    break;
                }
                if ($productStock->stock < $productCount) {
                    $fail('Quantity out of stock');
                    break;
                }
                break;

            case 'App\Http\Controllers\OrderController@continue':
                $order = Order::query()->find($value);
                foreach ($order->products as $product) {
                    $productStock = $product->stocks()->firstWhere('warehouse_id', $order->warehouse_id);
                    if (empty($productStock)) {
                        $fail('Product not in this stock');
                        break;
                    }
                    if ($productStock->stock < $product->pivot->count) {
                        $fail('Quantity out of stock');
                        break;
                    }
                }
                break;
        }
    }
}
