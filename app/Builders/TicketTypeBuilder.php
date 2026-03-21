<?php

declare(strict_types=1);

namespace App\Builders;

use App\Models\Event;

final class TicketTypeBuilder extends AppBuilder
{
    public function forEvent(Event $event): self
    {
        return $this->where('event_id', $event->id);
    }

    public function active(): self
    {
        return $this->where('is_active', true);
    }
}
