<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Events\OrderCompleted;
use App\Events\OrderStatusChanged;
use App\Jobs\GenerateTicketQrCodesJob;
use App\Models\Order;
use App\Notifications\OrderConfirmedNotification;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Database\DatabaseManager;

final readonly class CompleteOrderAction
{
    public function __construct(
        private DatabaseManager $databaseManager,
        private BusDispatcher $busDispatcher,
        private EventDispatcher $eventDispatcher,
        private RecordActivityAction $recordActivityAction,
    ) {}

    public function execute(Order $order): Order
    {
        if (OrderStatus::Paid === $order->status) {
            return $order;
        }

        $this->databaseManager->transaction(function () use ($order): void {
            $order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => CarbonImmutable::now(),
            ]);

            $order->tickets()
                ->where('status', TicketStatus::Pending)
                ->update(['status' => TicketStatus::Active]);

            $this->recordActivityAction->execute(ActivityEvent::OrderCompleted, $order);

            $order->loadMissing(['user', 'event', 'tickets']);
            $order->user->notify(new OrderConfirmedNotification($order));

            $this->busDispatcher->dispatch(new GenerateTicketQrCodesJob($order));
        });

        $this->eventDispatcher->dispatch(new OrderCompleted($order));
        $this->eventDispatcher->dispatch(new OrderStatusChanged($order));

        return $order;
    }
}
