<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $method = $this->faker->randomElement(['cash', 'bank_transfer', 'credit_card']);
        $status = $this->faker->numberBetween(1, 2);
        $amount = $this->faker->numberBetween(100_000, 3_000_000);
        $transactionId = "pi_" . $this->faker->unique()->regexify('[A-Za-z0-9]{24}');
        $transactionData = [
            'id' => $transactionId,
            'status' => $status === 1 ? 'succeeded' : 'failed',
            'amount' => $amount,
            'currency' => 'vnd',
            'payment_method' => $method,
        ];

        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'method' => $method,
            'status' => $status,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'transaction_data' => $transactionData,
            'paid_at' =>  $status === 1 ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
            'paid_at' => $attributes['paid_at'] ?? $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
            'paid_at' => null,
        ]);
    }
}
