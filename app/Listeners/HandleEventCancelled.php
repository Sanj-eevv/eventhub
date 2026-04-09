<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\TicketStatus;
use App\Events\EventCancelled;
use App\Models\Ticket;
use App\Notifications\EventCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

final class HandleEventCancelled implements ShouldQueue
{
    public function handle(EventCancelled $handler): void
    {
        $event = $handler->event;

        Ticket::query()
            ->forEvent($event)
            ->active()
            ->update(['status' => TicketStatus::Cancelled]);

        Ticket::query()
            ->forEvent($event)
            ->active()
            ->with('user')
            ->chunkById(100, function (Collection $tickets) use ($event): void {
                $tickets->unique('user_id')->each(function (Ticket $ticket) use ($event): void {
                    $ticket->user->notify(new EventCancelledNotification($event));
                });
            });
    }
}
