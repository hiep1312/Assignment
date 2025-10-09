<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    protected static ?array $userIds;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(rand(6, 12));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->realTextBetween(600, 5000),
            'author_id' => $this->faker->randomElement($userIds ??= User::pluck('id')->toArray()),
            'status' => $this->faker->numberBetween(0, 2),
        ];
    }

    public function users(Collection|array $userList): static
    {
        if($userList instanceof Collection) $userList = $userList->all();
        self::$userIds = array_map(fn($user) => $user instanceof User ? $user->id : $user, $userList);

        return $this;
    }

    public function draft(): static
    {
        return $this->state(fn(array $attributes) => ['status' => 0]);
    }

    public function published(): static
    {
        return $this->state(fn(array $attributes) => ['status' => 1]);
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => ['status' => 2]);
    }
}
