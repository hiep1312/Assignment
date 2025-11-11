<?php

namespace Database\Factories;

use App\Models\Banner;
use App\Models\Blog;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Imageable>
 */
class ImageableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $models = [Product::class, Banner::class, Blog::class];

        $modelType = $this->faker->randomElement($models);
        $modelId = $modelType::inRandomOrder()->value('id') ?? $modelType::factory()->create()->id;

        return [
            'image_id' => Image::factory(),
            'imageable_id' => $modelId,
            'imageable_type' => $modelType,
            'is_main' => false,
            'position' => null
        ];
    }

    public function product(): static
    {
        return $this->state(fn (array $attributes) => [
            'imageable_id' => Product::inRandomOrder()->value('id') ?? Product::factory()->create()->id,
            'imageable_type' => Product::class,
        ]);
    }

    public function banner(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_id' => Image::factory()->banner(),
            'imageable_id' => Banner::inRandomOrder()->value('id') ?? Banner::factory()->create()->id,
            'imageable_type' => Banner::class,
            'position' => $this->faker->unique()->numberBetween(1, 30),
        ]);
    }

    public function blog(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_id' => Image::factory()->blog(),
            'imageable_id' => Blog::inRandomOrder()->value('id') ?? Blog::factory()->create()->id,
            'imageable_type' => Blog::class,
        ]);
    }

    public function main(): static
    {
        return $this->state(fn (array $attributes) => ['is_main' => true]);
    }

    public function position(int $sortOrder): static
    {
        return $this->state(fn (array $attributes) => ['position' => $sortOrder]);
    }
}
