<?php

namespace App\Rules;

use App\Custom\Enums\OrderStatus;
use App\Models\Order;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Thе rule checks whether order's status is cancelled.
 */
class OrderIsCancelled implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $order = Order::find($value);
        if ($order->status == OrderStatus::ACTIVE->value) {
            $fail('Order is active');
        }
        if ($order->status == OrderStatus::COMPLETED->value) {
            $fail('Order already completed');
        }
    }
}
