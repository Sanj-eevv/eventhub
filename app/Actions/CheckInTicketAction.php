<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Enums\EventStatus;
use App\Enums\TicketStatus;
use App\Events\TicketCheckedIn;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Ticket;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Events\Dispatcher;
use RuntimeException;

final readonly class CheckInTicketAction
{
    public function __construct(
        private Dispatcher $dispatcher,
        private RecordActivityAction $recordActivityAction,
    ) {}

    public function __invoke(Ticket $ticket, User $scanner): Ticket
    {
        $ticket->loadMissing('event');

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

        ($this->recordActivityAction)(ActivityEvent::TicketCheckedIn, $ticket, $scanner);

        $this->dispatcher->dispatch(new TicketCheckedIn($ticket));

        return $ticket;
    }
}
