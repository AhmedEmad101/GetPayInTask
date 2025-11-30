<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Product;
use App\Models\Hold;
use Illuminate\Support\Str;

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
            'user_id' => User::factory(),           // create a user if not provided
            'product_id' => Product::factory(),     // create a product if not provided
            'hold_id' => Hold::factory(),           // create a hold if not provided
            'order_group_id' => 'grp-' . Str::random(6),
            'order_amount' => $this->faker->randomFloat(2, 50, 500),
            'payment_status' => 'pending',         // default pending
            'order_status' => 0,                    // default pre-payment
            'payment_method' => 'card',            // default
        ];
    }

    /**
     * Indicate the order is paid.
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);
    }

    /**
     * Indicate the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_status' => 'failed',
            'order_status' => 'cancelled',
        ]);
    }
}
