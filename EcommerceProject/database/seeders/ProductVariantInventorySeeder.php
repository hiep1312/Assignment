<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\ProductVariantInventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariantInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();
        $variantIds = ProductVariant::pluck('id');

        foreach($variantIds as $variantId){
            ProductVariantInventory::create([
                'variant_id' => $variantId,
                'stock' => ($stock = $faker->randomNumber(300)),
                'reserved' => $faker->randomNumber($stock < 30 ? $stock : 30),
                'sold_number' => $faker->randomNumber(1000)
            ]);
        }
    }
}
