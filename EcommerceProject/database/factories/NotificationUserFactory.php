<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Helpers\ResetsFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationUser>
 */
class NotificationUserFactory extends Factory
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
        $isRead = $this->faker->boolean();

        return [
            'notification_id' => Notification::factory(),
            'user_id' => $this->faker->unique()->randomElement($userIds ??= User::pluck('id')->toArray()),
            'is_read' => $isRead,
            'read_at' => $isRead ? $this->faker->dateTimeBetween('-2 month', 'now') : null,
        ];
    }

    public function users(Collection|array $userList): static
    {
        if($userList instanceof Collection) $userList = $userList->all();
        self::$userIds = array_map(fn($user) => $user instanceof User ? $user->id : $user, $userList);

        return $this;
    }

    public function read()
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => $attributes['read_at'] ?? $this->faker->dateTimeBetween('-2 month', 'now'),
        ]);
    }

    public function unread()
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }
}
