<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected static ?array $userIds;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => mb_ucfirst($name),
            'slug' => Str::slug($name),
            'created_by' => $this->faker->randomElement($userIds ??= User::pluck('id')->toArray()),
        ];
    }

    public function users(Collection|array $userList): static
    {
        if($userList instanceof Collection) $userList = $userList->all();
        self::$userIds = array_map(fn($user) => $user instanceof User ? $user->id : $user, $userList);

        return $this;
    }

    public function creator(User|int $user): static
    {
        return $this->state(fn (array $attributes) => ['created_by' => is_int($user) ? $user : $user->id]);
    }
}
