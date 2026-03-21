<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EventStatus;
use App\Enums\TicketStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Ticket;
use App\Models\User;
use Carbon\CarbonImmutable;
use RuntimeException;

final class CheckInTicketAction
{
    public function execute(Ticket $ticket, User $scanner): Ticket
    {
        if (EventStatus::Published !== $ticket->event->status) {
            throw new RuntimeException('Check-in is only available for published events.');
        }

        if ( ! $ticket->status->canTransitionTo(TicketStatus::Used)) {
            throw new InvalidStatusTransitionException($ticket->status, TicketStatus::Used);
        }

        $ticket->update([
            'status' => TicketStatus::Used,
            'checked_in_at' => CarbonImmutable::now(),
            'checked_in_by' => $scanner->id,
        ]);

        return $ticket;
    }
}
