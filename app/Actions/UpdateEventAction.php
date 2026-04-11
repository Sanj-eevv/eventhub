<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\EventData;
use App\DataTransferObjects\TicketTypeData;
use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\DatabaseManager;

final class UpdateEventAction
{
    public function __construct(
        private readonly DatabaseManager $databaseManager,
    ) {}

    public function execute(Event $event, EventData $data): Event
    {
        return $this->databaseManager->transaction(function () use ($event, $data): Event {
            $event->update([
                'title' => $data->title,
                'description' => $data->description,
                'starts_at' => $data->period->start,
                'ends_at' => $data->period->end,
                'timezone' => $data->timezone,
                'venue_name' => $data->venue_name,
                'address' => $data->address,
                'zip' => $data->zip,
                'map_url' => $data->map_url,
            ]);

            $this->syncTicketTypes($event, $data->ticket_types);

            return $event;
        });
    }

    /** @param TicketTypeData[] $ticketTypes */
    /** @param TicketTypeData[] $ticketTypes */
    private function syncTicketTypes(Event $event, array $ticketTypes): void
    {
        $existingUuids = $event->ticketTypes()->pluck('uuid');
        $submittedUuids = collect($ticketTypes)->pluck('uuid');
        $uuidsToKeep = $submittedUuids->intersect($existingUuids);

        $event->ticketTypes()
            ->whereNotIn('uuid', $uuidsToKeep)
            ->each(function (TicketType $ticketType): void {
                $ticketType->tickets()
                    ->where('status', TicketStatus::Pending)
                    ->update(['status' => TicketStatus::Cancelled]);

                $ticketType->delete();
            });

        foreach ($ticketTypes as $ticketType) {
            if ($existingUuids->contains($ticketType->uuid)) {
                $event->ticketTypes()
                    ->where('uuid', $ticketType->uuid)
                    ->update($this->ticketTypeAttributes($ticketType));
            } else {
                $event->ticketTypes()->create($this->ticketTypeAttributes($ticketType));
            }
        }
    }

    private function ticketTypeAttributes(TicketTypeData $ticketType): array
    {
        return [
            'name' => $ticketType->name,
            'description' => $ticketType->description,
            'price' => $ticketType->price,
            'capacity' => $ticketType->capacity,
            'max_per_user' => $ticketType->max_per_user,
            'sort_order' => $ticketType->sort_order,
            'sale_starts_at' => $ticketType->sale_starts_at,
            'sale_ends_at' => $ticketType->sale_ends_at,
        ];
    }
}
