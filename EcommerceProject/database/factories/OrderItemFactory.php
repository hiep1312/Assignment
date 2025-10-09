<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Helpers\ResetsFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    use ResetsFaker;

    protected static ?array $productVariantIds;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productVariantId = $this->faker->unique()->randomElement($productVariantIds ??= ProductVariant::pluck('id')->toArray());
        $variant = ProductVariant::find($productVariantId, ['price']);

        return [
            'order_id' => Order::factory(),
            'product_variant_id' => $productVariantId,
            'quantity' => $this->faker->numberBetween(1, 6),
            'price' => $variant?->price ?? $this->faker->numberBetween(60_000, 500_000),
        ];
    }

    public function productVariants(Collection|array $productVariantList): static
    {
        if($productVariantList instanceof Collection) $productVariantList = $productVariantList->all();
        self::$productVariantIds = array_map(fn($variant) => $variant instanceof ProductVariant ? $variant->id : $variant, $productVariantList);

        return $this;
    }
}
