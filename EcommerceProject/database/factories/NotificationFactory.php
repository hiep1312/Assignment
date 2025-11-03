<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 0,
            'title' => ucfirst($this->faker->sentence(rand(4, 10))),
            'message' => $this->faker->paragraph(rand(2, 5)),
            'variable' => null
        ];
    }

    public function order()
    {
        return $this->state(fn (array $attributes) => ['type' => 1]);
    }

    public function payment()
    {
        return $this->state(fn (array $attributes) => ['type' => 2]);
    }

    public function promotion()
    {
        return $this->state(fn (array $attributes) => ['type' => 3]);
    }

    public function account()
    {
        return $this->state(fn (array $attributes) => ['type' => 4]);
    }

    public function maintenance()
    {
        return $this->state(fn (array $attributes) => ['type' => 5]);
    }

    public function system()
    {
        return $this->state(fn (array $attributes) => ['type' => 6]);
    }
}
