<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\TicketStatus;
use App\Events\EventCancelled;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;

final class VoidEventTickets implements ShouldQueue
{
    public function handle(EventCancelled $event): void
    {
        Ticket::query()
            ->forEvent($event->event)
            ->active()
            ->update(['status' => TicketStatus::Cancelled]);
    }
}
