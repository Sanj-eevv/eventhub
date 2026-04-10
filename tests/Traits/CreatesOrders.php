<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use App\ValueObjects\BookingReference;
use Carbon\CarbonImmutable;

trait CreatesOrders
{
    protected function createReservedOrder(User $user, Event $event, ?TicketType $ticketType = null): Order
    {
        $ticketType ??= TicketType::factory()->create(['event_id' => $event->id]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => OrderStatus::Reserved,
            'total' => $ticketType->price,
            'subtotal' => $ticketType->price,
            'reserved_at' => CarbonImmutable::now(),
            'expires_at' => CarbonImmutable::now()->addMinutes(15),
        ]);

        $order->tickets()->create([
            'ticket_type_id' => $ticketType->id,
            'event_id' => $event->id,
            'user_id' => $user->id,
            'booking_reference' => (string) BookingReference::generate(),
            'attendee_name' => $user->name,
            'attendee_email' => $user->email,
            'status' => TicketStatus::Pending,
        ]);

        return $order;
    }

    protected function createPaidOrder(User $user, Event $event, ?TicketType $ticketType = null): Order
    {
        $ticketType ??= TicketType::factory()->create(['event_id' => $event->id]);

        $order = Order::factory()->paid()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total' => $ticketType->price,
            'subtotal' => $ticketType->price,
            'stripe_payment_intent_id' => 'pi_test_'.uniqid(),
        ]);

        $order->tickets()->create([
            'ticket_type_id' => $ticketType->id,
            'event_id' => $event->id,
            'user_id' => $user->id,
            'booking_reference' => (string) BookingReference::generate(),
            'attendee_name' => $user->name,
            'attendee_email' => $user->email,
            'status' => TicketStatus::Active,
        ]);

        return $order;
    }
}
