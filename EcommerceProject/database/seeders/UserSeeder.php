<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Create default users */
        $usersAdded = User::factory(2)->verified()->sequence(
            [
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => Hash::make('admin1234'),
                'first_name' => 'Admin',
                'last_name' => 'System',
                'role' => 'admin',
            ],
            [
                'email' => 'user@example.com',
                'username' => 'user',
                'password' => Hash::make('user1234'),
                'first_name' => 'User',
                'last_name' => 'System',
                'role' => 'user',
            ],
        )->create();

        /* Add 10 random users */
        $usersAdded = $usersAdded->concat(User::factory(20)->create());

        /* Create random user addresses */
        foreach ($usersAdded->pluck('id') as $userId) {
            UserAddress::factory(rand(1, 3))->create(new Sequence(
                fn(Sequence $sequence) => [
                    'user_id' => $userId,
                    'is_default' => $sequence->index === 0
                ]
            ));
        }
    }
}
