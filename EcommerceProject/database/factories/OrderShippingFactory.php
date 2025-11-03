<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderShipping>
 */
class OrderShippingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'recipient_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'province' => $this->faker->city(),
            'district' => $this->faker->citySuffix(),
            'ward' => $this->faker->streetSuffix(),
            'street' => $this->faker->optional(0.7)->streetAddress(),
            'postal_code' => $this->faker->optional()->postcode(),
            'note' => $this->faker->optional()->realTextBetween(50, 300)
        ];
    }
}
