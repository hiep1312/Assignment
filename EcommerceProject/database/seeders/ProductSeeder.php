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
        /* Create random 25 products with images and categories */
        $products = Product::factory(23)->has(
            Image::factory(4)->product()->state(new Sequence(
                fn (Sequence $sequence) => [
                    'is_main' => $sequence->index === 0,
                    'position' => $sequence->index
                ]
            )),
            'images'
        )->has(
            Category::factory(2),
            'categories'
        )->create();

        foreach($products->pluck('id') as $productId) {
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
