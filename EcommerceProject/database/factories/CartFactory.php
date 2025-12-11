<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    protected static ?array $userIds = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $forUser = $this->faker->boolean(self::$userIds ? 100 : 50);
        $userId = $guestToken = $expiresAt = null;
        $status = rand(1, 2);

        switch($forUser){
            case true:
                $userId = $this->faker->unique()->randomElement(self::$userIds ?? [User::factory()]);
                break;
            case false:
                $guestToken = $this->faker->userAgent();
                break;
        }

        $expiresAt = $status === 2 ? $this->faker->dateTimeBetween('-1 month', 'now') : $this->faker->dateTimeBetween('now', '+5 days');

        return [
            'user_id' => $userId,
            'guest_token' => $guestToken,
            'status' => $status,
            'expires_at' => $expiresAt,
        ];
    }

    public function users(Collection|array|null $userList = null): static
    {
        if($userList instanceof Collection) $userList = $userList->all();
        self::$userIds = is_array($userList) ? array_map(fn($user) => $user instanceof User ? $user->id : $user, $userList) : User::pluck('id')->toArray();

        return $this;
    }
}
