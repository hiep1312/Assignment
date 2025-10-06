<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();
        $pathAvatars = storage_path('app/public/avatars');
        if(!file_exists($pathAvatars)) mkdir($pathAvatars, 0777, true);

        /* Data default */
        User::insert([
            [
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => Hash::make('admin1234'),
                'first_name' => 'Admin',
                'last_name' => 'System',
                'birthday' => $faker->date(),
                'avatar' => basename($pathAvatars) . $faker->image($pathAvatars, 300, 300, false, null, true, false, null, FakerPicsumImagesProvider::WEBP_IMAGE),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'user@example.com',
                'username' => 'user',
                'password' => Hash::make('user1234'),
                'first_name' => 'User',
                'last_name' => 'System',
                'birthday' => $faker->date(),
                'avatar' => basename($pathAvatars) . $faker->image($pathAvatars, 300, 300, false, null, true, false, null, FakerPicsumImagesProvider::WEBP_IMAGE),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /* Data fake */
        for($i = 0; $i < 10; $i++){
            User::create([
                'email' => $faker->unique()->freeEmail(),
                'username' => $faker->unique()->userName(),
                'password' => Hash::make('12345678'),
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'birthday' => $faker->optional()->date(),
                'avatar' => basename($pathAvatars) . $faker->image($pathAvatars, 300, 300, false, null, true, false, null, FakerPicsumImagesProvider::WEBP_IMAGE),
                'role' => rand(0, 100) <= 80 ? 'user' : 'admin',
                'email_verified_at' => $faker->optional(0.3)->dateTime(),
            ]);
        }
    }
}
