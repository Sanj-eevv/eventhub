<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Media;
use App\Models\Organization;
use App\Models\TicketType;

trait CreatesEvents
{
    protected function createDraftEvent(?Organization $organization = null): Event
    {
        return Event::factory()
            ->state(['status' => EventStatus::Draft])
            ->for($organization ?? Organization::factory()->approved()->create())
            ->create();
    }

    protected function createPublishedEvent(?Organization $organization = null): Event
    {
        $event = Event::factory()
            ->published()
            ->for($organization ?? Organization::factory()->approved()->create())
            ->create();

        $this->attachCoverImage($event);

        return $event;
    }

    protected function createPublishedEventWithTicketType(?Organization $organization = null): array
    {
        $event = $this->createPublishedEvent($organization);

        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 1000,
            'capacity' => 100,
            'max_per_user' => null,
            'sale_starts_at' => now()->subDay(),
            'sale_ends_at' => now()->addMonth(),
        ]);

        return [$event, $ticketType];
    }

    protected function attachCoverImage(Event $event): Media
    {
        return $event->media()->create([
            'disk' => 'local',
            'path' => 'events/cover.jpg',
            'filename' => 'cover.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'is_cover' => true,
            'sort_order' => 0,
        ]);
    }
}
