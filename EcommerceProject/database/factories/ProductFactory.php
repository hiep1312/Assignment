<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(5),
            'status' => $this->faker->randomElement([0, 1])
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
