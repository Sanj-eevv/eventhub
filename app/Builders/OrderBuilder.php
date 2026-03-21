<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\User;

final class OrderBuilder extends AppBuilder
{
    public function forUser(User $user): self
    {
        return $this->where('user_id', $user->id);
    }

    public function forEvent(Event $event): self
    {
        return $this->where('event_id', $event->id);
    }

    public function expired(): self
    {
        return $this->where('status', OrderStatus::Expired);
    }

    public function paid(): self
    {
        return $this->where('status', OrderStatus::Paid);
    }
}
