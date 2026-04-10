<?php

declare(strict_types=1);

use App\Models\Event;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesUsers;

uses(CreatesEvents::class, CreatesUsers::class);

it('shows the homepage to guests', function (): void {
    $this->get(route('home'))->assertSuccessful();
});

it('shows the browse events page to guests', function (): void {
    $this->get(route('events.index'))->assertSuccessful();
});

it('shows published events on the browse page', function (): void {
    $event = $this->createPublishedEvent();

    $this->get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->has('events'));
});

it('does not expose draft events to guests', function (): void {
    $draft = $this->createDraftEvent();

    $this->get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn ($page) => $page
                ->where('events.data', fn ($data) => collect($data)->pluck('slug')->doesntContain($draft->slug))
        );
});

it('does not expose cancelled events to guests', function (): void {
    $cancelled = Event::factory()->cancelled()->create();

    $this->get(route('events.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn ($page) => $page
                ->where('events.data', fn ($data) => collect($data)->pluck('slug')->doesntContain($cancelled->slug))
        );
});

it('shows the individual page for a published event', function (): void {
    $event = $this->createPublishedEvent();

    $this->get(route('events.show', ['event' => $event->slug]))->assertSuccessful();
});

it('returns 404 for a draft event detail page', function (): void {
    $event = $this->createDraftEvent();

    $this->get(route('events.show', ['event' => $event->slug]))->assertNotFound();
});

it('returns 404 for a cancelled event detail page', function (): void {
    $event = Event::factory()->cancelled()->create();

    $this->get(route('events.show', ['event' => $event->slug]))->assertNotFound();
});
