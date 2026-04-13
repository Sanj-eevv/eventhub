<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class TicketCheckedIn implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('checkin.'.$this->ticket->event->uuid)];
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'ticket_uuid' => $this->ticket->uuid,
            'attendee' => $this->ticket->order->user->name,
            'ticket_type' => $this->ticket->ticketType->name,
            'checked_in_at' => $this->ticket->checked_in_at,
            'total_checked_in' => $this->ticket->event->tickets()->used()->count(),
            'total_tickets' => $this->ticket->event->tickets()->count(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ticket.checked-in';
    }
}
