<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();

        for ($i = 0; $i < 30; $i++) {
            Product::create([
                'title' => ($title = $faker->unique()->words(3, true)),
                'slug' => Str::slug($title),
                'description' => $faker->paragraph(5),
                'status' => $faker->randomElement([0, 1])
            ]);
        }
    }
}
