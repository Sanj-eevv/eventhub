<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;

/** @extends AppBuilder<Ticket> */
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

    public function used(): self
    {
        return $this->where('status', TicketStatus::Used);
    }

    public function byBookingReference(string $reference): self
    {
        return $this->where('booking_reference', $reference);
    }

    public function sold(): self
    {
        return $this->whereIn('status', [TicketStatus::Active, TicketStatus::Used]);
    }

    public function forOrganization(?int $organizationId): self
    {
        return $this->whereHas('event', fn (Builder $query) => $query->where('organization_id', $organizationId));
    }
}
