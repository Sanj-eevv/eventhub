<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\EventData;
use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

final class CreateEventAction
{
    public function execute(EventData $data): Event
    {
        return DB::transaction(function () use ($data): Event {
            $event = Event::query()->create([
                'user_id' => $data->user_id,
                'organization_id' => $data->organization_id,
                'title' => $data->title,
                'description' => $data->description,
                'starts_at' => $data->starts_at,
                'ends_at' => $data->ends_at,
                'timezone' => $data->timezone,
                'location' => $data->location,
                'status' => EventStatus::Draft,
            ]);

            foreach ($data->ticket_types as $ticketType) {
                $event->ticketTypes()->create([
                    'name' => $ticketType->name,
                    'price' => $ticketType->price,

                    'capacity' => $ticketType->capacity,
                    'max_per_user' => $ticketType->max_per_user,
                    'sort_order' => $ticketType->sort_order,
                    'is_active' => $ticketType->is_active,
                    'sale_starts_at' => $ticketType->sale_starts_at,
                    'sale_ends_at' => $ticketType->sale_ends_at,
                ]);
            }

            return $event;
        });
    }
}
