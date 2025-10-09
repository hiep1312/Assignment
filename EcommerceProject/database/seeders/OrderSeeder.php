<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShipping;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Create random orders with related */
        $orders = Order::factory(40)->has(
            OrderShipping::factory(1),
            'shipping'
        )->has(
            Payment::factory(1)->state(fn(array $attributes, Order $order) => [
                'user_id' => $order->user_id
            ]),
            'payment'
        )->create();

        /* Create random order items */
        foreach($orders->pluck('id') as $orderId) {
            OrderItem::factory(rand(1, 5))->resetFaker()->create([
                'order_id' => $orderId
            ]);
        }

        /* Calculate total amount */
        $orders->each(function(Order $order) {
            $order->load('items');
            $totalAmount = $order->items->sum(fn(OrderItem $item) => $item->price * $item->quantity);

            $order->update(['total_amount' => $totalAmount]);
            $order->payment()->update(['amount' => $totalAmount + $order->shipping_fee]);
        });
    }
}
