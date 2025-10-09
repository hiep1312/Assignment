<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariantInventory>
 */
class ProductVariantInventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stock = $this->faker->numberBetween(0,300);

        return [
            'variant_id' => ProductVariant::factory(),
            'stock' => $stock,
            'reserved' => $this->faker->numberBetween(0, $stock < 30 ? $stock : 30),
            'sold_number' => $this->faker->numberBetween(0, 1000),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'reserved' => 0,
            'sold_number' => 0
        ]);
    }
}
