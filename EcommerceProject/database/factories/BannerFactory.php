<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'link_url' => $this->faker->url(),
            'position' => $this->faker->unique()->numberBetween(1, 30),
            'status' => $this->faker->randomElement([1, 2])
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 1]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 2]);
    }
}
