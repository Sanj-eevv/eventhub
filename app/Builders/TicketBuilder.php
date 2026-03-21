<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\TicketStatus;
use App\Models\Event;

final class TicketBuilder extends AppBuilder
{
    public function forEvent(Event $event): self
    {
        return $this->where('event_id', $event->id);
    }

    public function active(): self
    {
        return $this->where('status', TicketStatus::Active);
    }

    public function byBookingReference(string $reference): self
    {
        return $this->where('booking_reference', $reference);
    }
}
