<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
final class OrderFactory extends Factory
{
    public function definition(): array
    {
        $now = CarbonImmutable::now();
        $subtotal = $this->faker->numberBetween(1000, 50000);

        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'status' => OrderStatus::Reserved,
            'currency' => 'USD',
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'reserved_at' => $now,
            'expires_at' => $now->addMinutes(15),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (): array => [
            'status' => OrderStatus::Paid,
            'paid_at' => CarbonImmutable::now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'status' => OrderStatus::Reserved,
            'expires_at' => CarbonImmutable::now()->subMinutes(15),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => [
            'status' => OrderStatus::Cancelled,
            'cancelled_at' => CarbonImmutable::now(),
        ]);
    }
}
