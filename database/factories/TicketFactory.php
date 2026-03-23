<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\ValueObjects\BookingReference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
final class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'ticket_type_id' => TicketType::factory(),
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'booking_reference' => fn () => (string) BookingReference::generate(),
            'attendee_name' => $this->faker->name,
            'attendee_email' => $this->faker->safeEmail,
            'status' => TicketStatus::Pending,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => TicketStatus::Active]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => TicketStatus::Cancelled]);
    }

    public function used(): static
    {
        return $this->state(fn () => ['status' => TicketStatus::Used]);
    }
}
