<?php

declare(strict_types=1);

use App\Enums\EventStatus;
use App\Notifications\EventCancelledNotification;
use Illuminate\Support\Facades\Notification;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('allows admins to publish a draft event that has a cover image', function (): void {
    $event = $this->createDraftEvent();
    $this->attachCoverImage($event);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.publish', $event))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($event->fresh()->status)->toBe(EventStatus::Published);
});

it('blocks publishing a draft event without a cover image', function (): void {
    $event = $this->createDraftEvent();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.publish', $event))
        ->assertRedirect()
        ->assertSessionHas('toast_error');

    expect($event->fresh()->status)->toBe(EventStatus::Draft);
});

it('blocks publishing an already-published event', function (): void {
    $event = $this->createPublishedEvent();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.publish', $event))
        ->assertRedirect()
        ->assertSessionHas('toast_error');
});

it('allows admins to unpublish a published event', function (): void {
    $event = $this->createPublishedEvent();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.unpublish', $event))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($event->fresh()->status)->toBe(EventStatus::Draft);
});

it('blocks unpublishing a draft event', function (): void {
    $event = $this->createDraftEvent();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.unpublish', $event))
        ->assertRedirect()
        ->assertSessionHas('toast_error');
});

it('allows admins to cancel a published event', function (): void {
    $event = $this->createPublishedEvent();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.cancel', $event))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($event->fresh()->status)->toBe(EventStatus::Cancelled);
});

it('blocks cancelling an already-cancelled event', function (): void {
    $event = $this->createPublishedEvent();
    $event->update(['status' => EventStatus::Cancelled]);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.cancel', $event))
        ->assertRedirect()
        ->assertSessionHas('toast_error');
});

it('sends cancellation notifications to paid order customers when an event is cancelled', function (): void {
    Notification::fake();

    $event = $this->createPublishedEvent();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType($event->organization);

    $customer = $this->createUser();
    $this->createPaidOrder($customer, $event, $ticketType);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.cancel', $event));

    Notification::assertSentTo($customer, EventCancelledNotification::class);
});

it('forbids organization admins from publishing another organizations event', function (): void {
    $event = $this->createDraftEvent();
    $this->attachCoverImage($event);

    $this->actingAs($this->createOrganizationAdmin())
        ->post(route('dashboard.events.publish', $event))
        ->assertForbidden();
});

it('logs activity when an event is published', function (): void {
    $event = $this->createDraftEvent();
    $this->attachCoverImage($event);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.publish', $event));

    $this->assertDatabaseHas('activity_logs', ['event' => 'event.published']);
});

it('logs activity when an event is cancelled', function (): void {
    $event = $this->createPublishedEvent();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.cancel', $event));

    $this->assertDatabaseHas('activity_logs', ['event' => 'event.cancelled']);
});
