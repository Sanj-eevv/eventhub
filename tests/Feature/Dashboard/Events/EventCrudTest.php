<?php

declare(strict_types=1);

use App\Models\Organization;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class);

function validEventPayload(string $organizationUuid): array
{
    return [
        'organization_uuid' => $organizationUuid,
        'title' => 'Test Event',
        'description' => 'A test event description.',
        'starts_at' => now()->addMonth()->format('Y-m-d\TH:i'),
        'ends_at' => now()->addMonth()->addDay()->format('Y-m-d\TH:i'),
        'timezone' => 'UTC',
        'venue_name' => 'The Venue',
        'address' => '123 Event St',
        'zip' => '10001',
        'map_url' => null,
        'ticket_types' => [
            [
                'uuid' => '',
                'name' => 'General Admission',
                'description' => 'Standard entry ticket.',
                'price' => '25.00',
                'capacity' => 100,
                'max_per_user' => null,
                'sale_starts_at' => null,
                'sale_ends_at' => null,
            ],
        ],
    ];
}

it('allows admins to list events', function (): void {
    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.events.index'))
        ->assertSuccessful();
});

it('forbids users without permission from listing events', function (): void {
    $this->actingAs($this->createUser())
        ->get(route('dashboard.events.index'))
        ->assertForbidden();
});

it('restricts organization admins to their organization events', function (): void {
    $organization = Organization::factory()->approved()->create();
    $orgAdmin = $this->createOrganizationAdmin($organization);

    $ownEvent = $this->createDraftEvent($organization);
    $otherEvent = $this->createDraftEvent();

    $this->actingAs($orgAdmin)
        ->get(route('dashboard.events.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn ($page) => $page
                ->where('events.data', fn ($data): bool => collect($data)->pluck('uuid')->contains($ownEvent->uuid)
                    && collect($data)->pluck('uuid')->doesntContain($otherEvent->uuid))
        );
});

it('allows admins to view the create event form', function (): void {
    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.events.create'))
        ->assertSuccessful()
        ->assertInertia(
            fn ($page) => $page
                ->has('organizations')
                ->has('timezones')
        );
});

it('allows admins to create an event with ticket types', function (): void {
    $organization = Organization::factory()->approved()->create();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.store'), validEventPayload($organization->uuid))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    $this->assertDatabaseHas('events', ['title' => 'Test Event']);
    $this->assertDatabaseHas('ticket_types', ['name' => 'General Admission']);
});

it('validates required fields when creating an event', function (): void {
    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.store'), [])
        ->assertSessionHasErrors(['organization_uuid', 'title', 'description', 'starts_at', 'ends_at', 'timezone', 'venue_name', 'address', 'zip', 'ticket_types']);
});

it('allows admins to view the event edit form', function (): void {
    $event = $this->createDraftEvent();

    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.events.edit', $event))
        ->assertSuccessful();
});

it('allows admins to update an event', function (): void {
    $organization = Organization::factory()->approved()->create();
    $event = $this->createDraftEvent($organization);

    $payload = validEventPayload($organization->uuid);
    $payload['title'] = 'Updated Event Title';
    $payload['ticket_types'][0]['uuid'] = $event->ticketTypes()->first()?->uuid;

    $this->actingAs($this->createAdmin())
        ->put(route('dashboard.events.update', $event), $payload)
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($event->fresh()->title)->toBe('Updated Event Title');
});

it('allows admins to view an event', function (): void {
    $event = $this->createDraftEvent();

    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.events.show', $event))
        ->assertSuccessful();
});

it('allows admins to delete an event', function (): void {
    $event = $this->createDraftEvent();

    $this->actingAs($this->createAdmin())
        ->delete(route('dashboard.events.destroy', $event))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    $this->assertSoftDeleted('events', ['id' => $event->id]);
});

it('forbids organization admins from deleting another organizations event', function (): void {
    $event = $this->createDraftEvent();

    $this->actingAs($this->createOrganizationAdmin())
        ->delete(route('dashboard.events.destroy', $event))
        ->assertForbidden();
});
