<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        foreach (range(1, 35) as $i) {
            $userId = fake()->randomElement($userIds);
            $productId = fake()->randomElement($productIds);
            $quantity = fake()->numberBetween(1, 5);
            $productPrice = Product::select('price')->find($productId);

            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $productPrice->price,
            ]);
        }
    }
}
