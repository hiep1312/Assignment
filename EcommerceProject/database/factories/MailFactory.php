<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mail>
 */
class MailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence(rand(6, 10)),
            'body' => $this->faker->paragraph(rand(3, 10)),
            'variable' => null,
            'type' => 0
        ];
    }

    public function order(bool $success = true): static
    {
        return $this->state(fn(array $attributes) => ['type' => $success ? 1 : 2]);
    }

    public function shipping(): static
    {
        return $this->state(fn(array $attributes) => ['type' => 3]);
    }

    public function forgotPassword(): static
    {
        return $this->state(fn(array $attributes) => ['type' => 4]);
    }

    public function registerSuccess(): static
    {
        return $this->state(fn(array $attributes) => ['type' => 5]);
    }
}
