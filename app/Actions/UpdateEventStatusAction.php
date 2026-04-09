<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EventStatus;
use App\Events\EventCancelled;
use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\MissingEventCoverImageException;
use App\Models\Event;
use Illuminate\Events\Dispatcher;

final class UpdateEventStatusAction
{
    public function __construct(private readonly Dispatcher $dispatcher) {}

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
            $this->dispatcher->dispatch(new EventCancelled($event));
        }

        return $event;
    }
}
