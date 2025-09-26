<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::where('role', 'user')->pluck('id')->toArray();

        for($i = 0; $i < 25; $i++){
            Order::create([
                'user_id' => fake()->randomElement($userIds),
                'status' => fake()->randomElement(['pending', 'done', 'cancelled']),
                'total_amount' => fake()->numberBetween(100000, 1000000),
            ]);
        }
    }
}
