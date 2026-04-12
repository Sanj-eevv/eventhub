<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\TicketItemData;
use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Events\OrderReserved;
use App\Exceptions\ActiveReservationExistsException;
use App\Exceptions\EventNotAvailableException;
use App\Exceptions\InsufficientTicketCapacityException;
use App\Exceptions\TicketLimitExceededException;
use App\Exceptions\TicketSaleClosedException;
use App\Exceptions\TicketSaleNotOpenException;
use App\Jobs\ExpireOrderJob;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use App\Services\SettingsService;
use App\ValueObjects\BookingReference;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;

final readonly class ReserveTicketsAction
{
    public function __construct(
        private DatabaseManager $databaseManager,
        private Dispatcher $dispatcher,
        private EventDispatcher $eventDispatcher,
        private SettingsService $settingsService,
    ) {}

    /**
     * @param  array<int, TicketItemData>  $items
     */
    public function execute(User $user, Event $event, array $items): Order
    {
        throw_if(EventStatus::Published !== $event->status, EventNotAvailableException::class);

        throw_if(Order::query()->forUser($user)->forEvent($event)->activeReservation()->exists(), ActiveReservationExistsException::class);

        $attempts = 0;

        while (true) {
            try {
                $order = $this->databaseManager->transaction(function () use ($user, $event, $items): Order {
                    $reservationMinutes = $this->settingsService->get()->ticketReservationMinutes;
                    $now = CarbonImmutable::now();
                    $expiresAt = $now->addMinutes($reservationMinutes);

                    $resolved = collect($items)->map(function (TicketItemData $item) use ($now, $event, $user): array {
                        $ticketType = TicketType::query()
                            ->lockForUpdate()
                            ->where('uuid', $item->ticketTypeUuid)
                            ->firstOrFail();

                        $saleEndsAt = $ticketType->sale_ends_at ?? $event->ends_at;

                        throw_if($ticketType->sale_starts_at && $now->isBefore($ticketType->sale_starts_at), TicketSaleNotOpenException::class, $ticketType);

                        throw_if($now->isAfter($saleEndsAt), TicketSaleClosedException::class, $ticketType);

                        $soldCount = $ticketType->tickets()
                            ->whereIn('status', [TicketStatus::Pending, TicketStatus::Active])
                            ->count();

                        throw_if($soldCount + $item->quantity > $ticketType->capacity, InsufficientTicketCapacityException::class, $ticketType);

                        $userCount = $ticketType->tickets()
                            ->where('user_id', $user->id)
                            ->whereIn('status', [TicketStatus::Pending, TicketStatus::Active])
                            ->count();

                        throw_if(null !== $ticketType->max_per_user && $userCount + $item->quantity > $ticketType->max_per_user, TicketLimitExceededException::class, $ticketType);

                        return ['ticketType' => $ticketType, 'quantity' => $item->quantity];
                    });

                    $subtotal = $resolved->sum(fn (array $item): int => $item['ticketType']->price * $item['quantity']);

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

                    $resolved->each(function (array $item) use ($order, $event, $user): void {
                        Collection::times($item['quantity'], fn () => $order->tickets()->create([
                            'ticket_type_id' => $item['ticketType']->id,
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                            'booking_reference' => (string) BookingReference::generate(),
                            'attendee_name' => $user->name,
                            'attendee_email' => $user->email,
                            'status' => TicketStatus::Pending,
                        ]));
                    });

                    $this->dispatcher->dispatch((new ExpireOrderJob($order))->delay($expiresAt));

                    return $order;
                });

                $this->eventDispatcher->dispatch(new OrderReserved($order));

                return $order;
            } catch (UniqueConstraintViolationException $exception) {
                throw_if(++$attempts >= 3, $exception);
            }
        }
    }
}
