<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        $priceOriginal = $this->faker->numberBetween(80_000, 500_000);

        return [
            'product_id' => Product::factory(),
            'name' => $name,
            'sku' => strtoupper(Str::random(8)),
            'price' => $priceOriginal,
            'discount' => $this->faker->optional(0.7)->numberBetween(60000, $priceOriginal),
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 1]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 0]);
    }
}
