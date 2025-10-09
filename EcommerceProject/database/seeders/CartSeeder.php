<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Create random carts */
        $carts = Cart::factory(20)->users()->create();

        /* Create random cart items */
        foreach($carts->pluck('id') as $cartId) {
            CartItem::factory(rand(1, 5))->resetFaker()->create([
                'cart_id' => $cartId
            ]);
        }
    }
}
