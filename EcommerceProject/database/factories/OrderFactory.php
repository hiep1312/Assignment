<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected static ?array $userIds;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shippingFee = $this->faker->numberBetween(10_000, 60_000);
        $status = $this->faker->numberBetween(1, 9);

        /* Time event */
        $cancelledAt = $completedAt = $deliveredAt = $shippedAt = $processingAt = $confirmedAt = null;

        switch($status){
            case 9;
            case 8;
            case 7:
                $cancelledAt = now()->subDays(rand(0, 20));
                $processingAt = $this->faker->optional(0.7)->dateTimeBetween($cancelledAt->clone()->subDay(), $cancelledAt);
                $confirmedAt = $this->faker->optional($processingAt ? 0 : 0.5)->dateTimeBetween($processingAt ? (clone $processingAt)->modify('-' . rand(1, 2) . ' day') : $cancelledAt->clone()->subDays(rand(2, 3)), $processingAt ?? $cancelledAt);
                break;
            case 6: $completedAt = now()->subDays(rand(0, 14));
            case 5: $deliveredAt = $completedAt?->clone()->subDay() ?? now();
            case 4: $shippedAt = $deliveredAt?->clone()->subDays(ceil($shippingFee / 10000)) ?? now();
            case 3: $processingAt = $shippedAt?->clone()->subDay() ?? now();
            case 2: $confirmedAt = $processingAt?->clone()->subDays(rand(1, 2)) ?? now();
            case 1: break;
        }

        return [
            'user_id' => $this->faker->randomElement($userIds ??= User::pluck('id')->toArray()),
            'order_code' => $this->faker->uuid(),
            'total_amount' => $this->faker->numberBetween(100_000, 3_000_000),
            'shipping_fee' => $shippingFee,
            'status' => $status,
            'customer_note' => $this->faker->optional()->realTextBetween(50, 300),
            'admin_note' => $this->faker->optional()->realTextBetween(50, 300),
            'cancel_reason' => $status >= 7 ? $this->faker->sentence(rand(6, 20)) : null,
            'confirmed_at' => $confirmedAt,
            'processing_at' => $processingAt,
            'shipped_at' => $shippedAt,
            'delivered_at' => $deliveredAt,
            'completed_at' => $completedAt,
            'cancelled_at' => $cancelledAt
        ];
    }

    public function users(Collection|array $userList): static
    {
        if($userList instanceof Collection) $userList = $userList->all();
        self::$userIds = array_map(fn($user) => $user instanceof User ? $user->id : $user, $userList);

        return $this;
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
            'cancel_reason' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 6,
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 8,
            'cancel_reason' => 'Khách hàng hủy đơn hàng.',
            'cancelled_at' => now(),
        ]);
    }

    public function status(int $status, string $cancelReason = ''): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ($status > 0 && $status < 10) ? $status : 1,
            'cancel_reason' => ($status >= 7 && $status < 10) ? ($cancelReason ?: $attributes['cancel_reason']) : null,
        ]);
    }
}
