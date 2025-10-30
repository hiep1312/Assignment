<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Imageable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Create random banners with image */
        Banner::factory(7)->has(
            Imageable::factory(1)->banner(),
            'imageable'
        )->create();
    }
}
