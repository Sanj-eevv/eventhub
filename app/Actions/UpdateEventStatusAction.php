<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Enums\EventStatus;
use App\Events\EventCancelled;
use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\MissingEventCoverImageException;
use App\Models\Event;
use App\Models\User;
use Illuminate\Events\Dispatcher;

final readonly class UpdateEventStatusAction
{
    public function __construct(
        private Dispatcher $dispatcher,
        private RecordActivityAction $recordActivityAction,
    ) {}

    public function __invoke(Event $event, EventStatus $status, ?User $causer = null): Event
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
            ($this->recordActivityAction)(ActivityEvent::EventCancelled, $event, $causer);
        }

        if (EventStatus::Published === $status) {
            ($this->recordActivityAction)(ActivityEvent::EventPublished, $event, $causer);
        }

        return $event;
    }
}
