<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\EventData;
use App\Models\Event;

final class UpdateEventAction
{
    public function execute(Event $event, EventData $data): Event
    {
        $event->update([
            'user_id' => $data->user_id,
            'organization_id' => $data->organization_id,
            'title' => $data->title,
            'description' => $data->description,
            'starts_at' => $data->starts_at,
            'ends_at' => $data->ends_at,
            'timezone' => $data->timezone,
            'location' => $data->location,
            'tickets' => $data->tickets,
        ]);

        return $event;
    }
}
