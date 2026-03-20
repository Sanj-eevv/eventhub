<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EventStatus;
use App\Models\Event;

final class UpdateEventStatusAction
{
    public function execute(Event $event, EventStatus $status): Event
    {
        $validFromStates = match ($status) {
            EventStatus::Published => [EventStatus::Draft],
            EventStatus::Draft => [EventStatus::Published],
            EventStatus::Cancelled => [EventStatus::Published],
        };

        if ( ! in_array($event->status, $validFromStates, true)) {
            return $event;
        }

        $event->update(['status' => $status]);

        return $event;
    }
}
