<?php

namespace Database\Factories;

use App\Custom\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer' => $this->faker->name(),
            'status' => $this->faker->randomElement(OrderStatus::values()),
            'completed_at' => function (array $attributes) {
                return $attributes['status'] === OrderStatus::COMPLETED->value ? now() : null;
            },
        ];
    }
}
