<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin account',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'User account',
            'email' => 'user@example.com',
            'password' => Hash::make('user1234'),
            'role' => 'user'
        ]);

        for($i = 0; $i < 10; $i++){
            User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('12345678'),
                'role' => fake()->randomElement(['admin', 'user']),
            ]);
        }
    }
}
