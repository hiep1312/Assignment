<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pathAvatars = storage_path('app/public/avatars');
        if(!file_exists($pathAvatars)) mkdir($pathAvatars, 0777, true);

        return [
            'email' => $this->faker->unique()->freeEmail(),
            'username' => $this->faker->unique()->userName(),
            'password' => self::$password ??= Hash::make('12345678'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'birthday' => $this->faker->optional()->date('Y-m-d', '-5 years'),
            'avatar' => basename($pathAvatars) . "/" . $this->faker->image(dir: $pathAvatars, width: 300, height: 300, isFullPath: false, randomize: true, imageExtension: FakerPicsumImagesProvider::WEBP_IMAGE),
            'role' => rand(0, 100) <= 80 ? 'user' : 'admin',
            'email_verified_at' => $this->faker->optional(0.3)->dateTime(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
        ]);
    }
}
