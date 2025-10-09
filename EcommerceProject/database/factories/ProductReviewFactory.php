<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductReview>
 */
class ProductReviewFactory extends Factory
{
    protected static ?array $userIds;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => $this->faker->randomElement($userIds ??= User::pluck('id')->toArray()),
            'rating' => $this->faker->numberBetween(1, 5),
            'content' => $this->faker->optional(0.3)->realTextBetween(50, 400),
        ];
    }

    public function users(Collection|array $userList): static
    {
        if($userList instanceof Collection) $userList = $userList->all();
        self::$userIds = array_map(fn($user) => $user instanceof User ? $user->id : $user, $userList);

        return $this;
    }

    public function positive(): static
    {
        return $this->state(fn (array $attributes) => ['rating' => rand(4, 5)]);
    }

    public function neutral(): static
    {
        return $this->state(fn (array $attributes) => ['rating' => rand(2, 3)]);
    }

    public function negative(): static
    {
        return $this->state(fn (array $attributes) => ['rating' => 1]);
    }

    public function writeReview(string $content): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => $content,
        ]);
    }
}
