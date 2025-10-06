<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();
        $productIds = Product::pluck('id');

        foreach($productIds as $productId){
            foreach(range(1, rand(1, 5)) as $i){
                ProductVariant::create([
                    'product_id' => $productId,
                    'name' => $faker->words(2, true),
                    'sku' => strtoupper(Str::random(8)),
                    'price' => ($priceOriginal = $faker->numberBetween(80000, 500000)),
                    'discount' => $faker->optional(0.3)->numberBetween(60000, $priceOriginal),
                    'status' => $faker->randomElement([0, 1])
                ]);
            }
        }
    }
}
