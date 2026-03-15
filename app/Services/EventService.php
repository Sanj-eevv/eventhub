<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\EventDto;
use App\Enums\EventStatus;
use App\Models\Event;

final class EventService
{
    public function create(EventDto $data): Event
    {
        return Event::query()->create([...$data->toArray(), 'status' => EventStatus::Draft]);
    }

    public function update(Event $event, EventDto $data): Event
    {
        $event->update($data->toArray());

        return $event->refresh();
    }

    public function publish(Event $event): Event
    {
        $event->update(['status' => EventStatus::Published]);

        return $event->refresh();
    }

    public function cancel(Event $event): Event
    {
        $event->update(['status' => EventStatus::Cancelled]);

        return $event->refresh();
    }

    public function unpublish(Event $event): Event
    {
        $event->update(['status' => EventStatus::Draft]);

        return $event->refresh();
    }

    public function delete(Event $event): void
    {
        $event->delete();
    }
}
