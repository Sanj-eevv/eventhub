<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EventStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Event;

final class UpdateEventStatusAction
{
    public function execute(Event $event, EventStatus $status): Event
    {
        if ( ! $event->status->canTransitionTo($status)) {
            throw new InvalidStatusTransitionException($event->status, $status);
        }

        $event->update(['status' => $status]);

        return $event;
    }
}
