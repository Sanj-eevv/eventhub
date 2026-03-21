<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EventStatus;
use App\Events\EventCancelled;
use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\MissingEventCoverImageException;
use App\Models\Event;

final class UpdateEventStatusAction
{
    public function execute(Event $event, EventStatus $status): Event
    {
        if ( ! $event->status->canTransitionTo($status)) {
            throw new InvalidStatusTransitionException($event->status, $status);
        }

        if (EventStatus::Published === $status && ! $event->coverImage()->exists()) {
            throw new MissingEventCoverImageException();
        }

        $event->update(['status' => $status]);

        if (EventStatus::Cancelled === $status) {
            EventCancelled::dispatch($event);
        }

        return $event;
    }
}
