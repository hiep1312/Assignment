<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::pluck('id')->toArray();
        $productDataImage = [
            "https://270349907.e.cdneverest.net/fast/552x552/filters:format(webp)/vuanem.com/storage/products/1965/qJXuBclXwY0INjuyNgDjtZYBLc3Ex1PWu5ASbTCA_2025-09-18-103843.jpg",
            "https://270349907.e.cdneverest.net/fast/510x510/filters:format(webp)/vuanem.com/storage/products/1103/cdnEHrvHBVGTCtTMRe5lqcnXC3krNxG3W9sleAIX_2025-09-16-000022.png",
            "https://270349907.e.cdneverest.net/fast/510x510/filters:format(webp)/vuanem.com/storage/products/1348/RzEjeLP6PFs2h13vLLhv4PEOtLDiUh4aasVOHFEV_2025-09-16-000014.png",
            "https://270349907.e.cdneverest.net/fast/510x510/filters:format(webp)/vuanem.com/storage/products/1779/oKCLq6pXepl3p8FCgISoILJKIHl07xDiKBkAdKTK_2025-09-18-103843.png",
            "https://270349907.e.cdneverest.net/fast/510x510/filters:format(webp)/vuanem.com/storage/products/1187/X8QSyZ2oL8ggBTA6seuHWu4uzyZBId7jQ4xPSJqi_2025-09-16-000012.png",
            "https://270349907.e.cdneverest.net/fast/480x480/filters:format(webp)/vuanem.com/storage/products/1819/isIG5DXFcqiMzjxBYFt6w2o5gyhz04n58DwpdATG.jpg",
            "https://270349907.e.cdneverest.net/fast/480x480/filters:format(webp)/vuanem.com/storage/products/1606/tBBXNfB6oLkcdprj0CL7bGi59SMd7chTtp6ochG3.jpg",
            "https://270349907.e.cdneverest.net/fast/480x480/filters:format(webp)/vuanem.com/storage/products/1912/Y8fC6Wvs8kRbnpvxcU7ZJ5lSgbsYCXA7hB7jsqLH.jpg",
            "https://270349907.e.cdneverest.net/fast/480x480/filters:format(webp)/vuanem.com/storage/products/1892/4Dzuzejum6heoTP8EBqIVgeQl2gSlW8MCiSgZPnb.jpg",
            "https://270349907.e.cdneverest.net/fast/480x480/filters:format(webp)/vuanem.com/storage/products/1924/6urvuu4vMXWgACLOi3LE1qcTKF4JM6unk4TB23wN.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1750/HREzk4Xc90bnqeLAo3h7G85SxZ5AftoSnSpE8COu.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1618/jHo4frGwknEcmJVieD6yZcdXB9SryuF5dT23lb9Q.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1116/BWBGu0ZXHM8TTyjCYPDunvZKfKHqZQbrj2PLJQxD.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1365/DowRDvXgvtVrON56p18mvhlz1zAvqdpzcRIWENgX.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1881/K9Veupq3H50ZnyySCxQJhf7NuF0ZQQ8JZhHRV0Gm.png",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1602/GjleulLaolesU0w6XnklSNPVZcKXePXKPGt0V76k.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1617/fW6G24cSyAHFSKDBmO1MyCvYksMA5i8HIBD6Pwlg.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1173/9AreKTkxVTvMAf6oGyz6ISWjaB84AjJOEEvyRauB.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1355/Hf4QQJlFtTx7wKyCqlSXC8meBjRKWJH3WBijXDnD.jpg",
            "https://270349907.e.cdneverest.net/fast/240x240/filters:format(webp)/vuanem.com/storage/products/1330/rnrEyJ4ipzxBrjGINGtLtBA7Ag05zLYTxiQLVUZ2.jpg"
        ];

        for ($i = 0; $i < 20; $i++) {
            $dataImage = file_get_contents($productDataImage[$i], false, stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'ignore_errors' => true
                ],
            ]));

            $image = "products/" . uniqid('image_') . basename($productDataImage[$i]);
            Storage::drive('public')->put($image, $dataImage);

            Product::create([
                'name' => fake()->unique()->words(4, true),
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'price' => fake()->numberBetween(50000, 300000),
                'image_url' => fake()->imageUrl(640, 480, 'products', true),
                'description' => fake()->paragraph(),
                'status' => fake()->randomElement(['active', 'inactive']),
                'sold_number' => fake()->numberBetween(0, 500),
            ]);
        }
    }
}
