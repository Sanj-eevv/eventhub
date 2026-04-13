<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\EventData;
use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Database\DatabaseManager;

final readonly class CreateEventAction
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function __invoke(EventData $data): Event
    {
        return $this->databaseManager->transaction(function () use ($data): Event {
            $event = Event::query()->create([
                'user_id' => $data->user_id,
                'organization_id' => $data->organization_id,
                'title' => $data->title,
                'description' => $data->description,
                'starts_at' => $data->period->start,
                'ends_at' => $data->period->end,
                'timezone' => $data->timezone,
                'venue_name' => $data->venue_name,
                'address' => $data->address,
                'zip' => $data->zip,
                'map_url' => $data->map_url,
                'status' => EventStatus::Draft,
            ]);

            foreach ($data->ticket_types as $ticketType) {
                $event->ticketTypes()->create([
                    'name' => $ticketType->name,
                    'description' => $ticketType->description,
                    'price' => $ticketType->price,
                    'capacity' => $ticketType->capacity,
                    'max_per_user' => $ticketType->max_per_user,
                    'sort_order' => $ticketType->sort_order,
                    'sale_starts_at' => $ticketType->sale_starts_at,
                    'sale_ends_at' => $ticketType->sale_ends_at,
                ]);
            }

            return $event;
        });
    }
}
