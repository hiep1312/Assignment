<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ProductVariantInventory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Create 7 categories */
        $categories = Category::factory(7)->create();
        /* Create random 23 products with images */
        $products = Product::factory(23)->hasAttached(
            Image::factory(4)->product(),
            function() {
                static $index = 0;
                $index = $index % 4;

                return [
                    'is_main' => $index === 0,
                    'position' => $index++
                ];
            },
            'images'
        )->create();

        foreach($products->keyBy('id') as $productId => $product) {
            /* Attach 1–3 random categories from existing categories */
            $product->categories()->attach($categories->random(rand(1, 3))->pluck('id'));

            /* Create random variants with inventory */
            ProductVariant::factory(rand(1, 6))->has(
                ProductVariantInventory::factory(1),
                'inventory'
            )->create([
                'product_id' => $productId
            ]);

            /* Create random product reviews */
            ProductReview::factory(rand(5, 30))->create([
                'product_id' => $productId
            ]);
        }
    }
}
