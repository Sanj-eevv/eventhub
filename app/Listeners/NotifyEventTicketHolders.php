<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\EventCancelled;
use App\Models\Ticket;
use App\Notifications\EventCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

final class NotifyEventTicketHolders implements ShouldQueue
{
    public function handle(EventCancelled $event): void
    {
        Ticket::query()
            ->forEvent($event->event)
            ->active()
            ->with('user')
            ->chunkById(100, function (Collection $tickets) use ($event): void {
                $tickets->unique('user_id')->each(function (Ticket $ticket) use ($event): void {
                    $ticket->user->notify(new EventCancelledNotification($event->event));
                });
            });
    }
}
