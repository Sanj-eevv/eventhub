<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class DuplicateScanAttempted implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly Event $event,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('checkin.'.$this->event->uuid)];
    }

    public function broadcastWith(): array
    {
        return [
            'ticket_uuid' => $this->ticket->uuid,
            'attendee' => $this->ticket->order->user->name,
            'ticket_type' => $this->ticket->ticketType->name,
        ];
    }

    public function broadcastAs(): string
    {
        return 'ticket.duplicate-scan';
    }
}
