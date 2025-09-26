<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();

        for ($i = 0; $i < 5; $i++) {
            Category::create([
                'name' => fake()->unique()->words(2, true),
                'created_by' => $userIds[array_rand($userIds)],
            ]);
        }
    }
}
