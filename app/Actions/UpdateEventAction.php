<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\EventData;
use App\DataTransferObjects\TicketTypeData;
use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;

final class UpdateEventAction
{
    public function execute(Event $event, EventData $data): Event
    {
        return DB::transaction(function () use ($event, $data): Event {
            $event->update([
                'user_id' => $data->user_id,
                'organization_id' => $data->organization_id,
                'title' => $data->title,
                'description' => $data->description,
                'starts_at' => $data->starts_at,
                'ends_at' => $data->ends_at,
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
                    ->update([
                        'name' => $ticketType->name,
                        'price' => $ticketType->price,
                        'capacity' => $ticketType->capacity,
                        'max_per_user' => $ticketType->max_per_user,
                        'sort_order' => $ticketType->sort_order,
                        'sale_starts_at' => $ticketType->sale_starts_at,
                        'sale_ends_at' => $ticketType->sale_ends_at,
                    ]);
            } else {
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
        }
    }
}
