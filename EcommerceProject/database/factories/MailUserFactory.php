<?php

namespace Database\Factories;

use App\Models\Mail;
use App\Models\User;
use App\Helpers\ResetsFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MailUser>
 */
class MailUserFactory extends Factory
{
    use ResetsFaker;

    protected static ?array $userIds;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->numberBetween(1, 2);

        return [
            'mail_id' => Mail::factory(),
            'user_id' => $this->faker->unique()->randomElement($userIds ??= User::pluck('id')->toArray()),
            'status' => $status,
            'sent_at' => $status === 1 ? $this->faker->dateTimeBetween('-2 month', 'now') : null,
            'error_message' => $status === 2 ? $this->faker->sentence(rand(8, 50)) : null,
        ];
    }

    public function users(Collection|array $userList): static
    {
        if($userList instanceof Collection) $userList = $userList->all();
        self::$userIds = array_map(fn($user) => $user instanceof User ? $user->id : $user, $userList);

        return $this;
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
            'sent_at' => null,
            'error_message' => null
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 1,
            'sent_at' => $attributes['sent_at'] ?? $this->faker->dateTimeBetween('-2 month', 'now'),
            'error_message' => null
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 2,
            'sent_at' => null,
            'error_message' => $attributes['error_message'] ?? $this->faker->sentence(rand(8, 50))
        ]);
    }
}
