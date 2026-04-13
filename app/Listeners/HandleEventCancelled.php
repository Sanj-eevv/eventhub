<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\CancelPaidOrderAction;
use App\Enums\TicketStatus;
use App\Events\EventCancelled;
use App\Jobs\ProcessRefundJob;
use App\Models\Order;
use App\Models\Ticket;
use App\Notifications\EventCancelledNotification;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

final readonly class HandleEventCancelled implements ShouldQueue
{
    public function __construct(
        private CancelPaidOrderAction $cancelPaidOrderAction,
        private Dispatcher $dispatcher,
    ) {}

    public function handle(EventCancelled $eventCancelled): void
    {
        $event = $eventCancelled->event;

        Ticket::query()
            ->forEvent($event)
            ->active()
            ->update(['status' => TicketStatus::Cancelled]);

        Order::query()
            ->forEvent($event)
            ->paid()
            ->with('user')
            ->chunkById(100, function (Collection $orders) use ($event): void {
                $orders->each(function (Order $order) use ($event): void {
                    ($this->cancelPaidOrderAction)($order);
                    $order->user->notify(new EventCancelledNotification($event));
                    $this->dispatcher->dispatch(new ProcessRefundJob($order, $order->total));
                });
            });
    }
}
