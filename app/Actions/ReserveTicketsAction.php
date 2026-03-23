<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Exceptions\InsufficientTicketCapacityException;
use App\Exceptions\TicketLimitExceededException;
use App\Exceptions\TicketSaleClosedException;
use App\Exceptions\TicketSaleNotOpenException;
use App\Jobs\ExpireOrderJob;
use App\Models\Event;
use App\Models\Order;
use App\Models\Setting;
use App\Models\TicketType;
use App\Models\User;
use App\ValueObjects\BookingReference;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class ReserveTicketsAction
{
    /**
     * @param  array<int, array{ticket_type_uuid: string, quantity: int}>  $items
     */
    public function execute(User $user, Event $event, array $items): Order
    {
        return DB::transaction(function () use ($user, $event, $items): Order {
            $reservationMinutes = (int) Setting::get('ticket_reservation_minutes', default: 15);
            $now = CarbonImmutable::now();
            $expiresAt = $now->addMinutes($reservationMinutes);
            $subtotal = 0;
            $resolved = [];

            foreach ($items as $item) {
                $ticketType = TicketType::query()
                    ->lockForUpdate()
                    ->where('uuid', $item['ticket_type_uuid'])
                    ->firstOrFail();

                $saleEndsAt = $ticketType->sale_ends_at ?? $event->ends_at;

                if ($ticketType->sale_starts_at && $now->isBefore($ticketType->sale_starts_at)) {
                    throw new TicketSaleNotOpenException($ticketType);
                }

                if ($now->isAfter($saleEndsAt)) {
                    throw new TicketSaleClosedException($ticketType);
                }

                $soldCount = $ticketType->tickets()
                    ->whereIn('status', [TicketStatus::Pending, TicketStatus::Active])
                    ->count();

                if ($soldCount + $item['quantity'] > $ticketType->capacity) {
                    throw new InsufficientTicketCapacityException($ticketType);
                }

                $userCount = $ticketType->tickets()
                    ->where('user_id', $user->id)
                    ->whereIn('status', [TicketStatus::Pending, TicketStatus::Active])
                    ->count();

                if (null !== $ticketType->max_per_user && $userCount + $item['quantity'] > $ticketType->max_per_user) {
                    throw new TicketLimitExceededException($ticketType);
                }

                $subtotal += $ticketType->price * $item['quantity'];
                $resolved[] = ['ticketType' => $ticketType, 'quantity' => $item['quantity']];
            }

            $order = Order::query()->create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => OrderStatus::Reserved,
                'currency' => 'USD',
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'reserved_at' => $now,
                'expires_at' => $expiresAt,
            ]);

            foreach ($resolved as $item) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $order->tickets()->create([
                        'ticket_type_id' => $item['ticketType']->id,
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'booking_reference' => (string) BookingReference::generate(),
                        'attendee_name' => $user->name,
                        'attendee_email' => $user->email,
                        'status' => TicketStatus::Pending,
                    ]);
                }
            }

            ExpireOrderJob::dispatch($order)->delay($expiresAt);

            return $order;
        });
    }
}
