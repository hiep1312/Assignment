<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderIds = Order::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        foreach ($orderIds as $orderId) {
            foreach(range(1, rand(2, 5)) as $value){
                $productId = fake()->randomElement($productIds);
                $quantity = fake()->numberBetween(1, 5);
                $productPrice = Product::select('price')->find($productId);

                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $productPrice->price,
                ]);
            }
        }
    }
}
