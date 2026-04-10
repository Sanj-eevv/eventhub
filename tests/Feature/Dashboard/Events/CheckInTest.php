<?php

declare(strict_types=1);

use App\Enums\TicketStatus;
use App\Models\Organization;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('allows users with check-in permission to view the check-in interface', function (): void {
    $event = $this->createPublishedEvent();

    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.events.check-in', $event))
        ->assertSuccessful();
});

it('forbids users without check-in permission', function (): void {
    $event = $this->createPublishedEvent();

    $this->actingAs($this->createUser())
        ->get(route('dashboard.events.check-in', $event))
        ->assertForbidden();
});

it('marks an active ticket as Used on a valid scan', function (): void {
    $organization = Organization::factory()->approved()->create();
    $event = $this->createPublishedEvent($organization);
    [$event, $ticketType] = $this->createPublishedEventWithTicketType($organization);
    $customer = $this->createUser();
    $order = $this->createPaidOrder($customer, $event, $ticketType);
    $ticket = $order->tickets()->first();

    $scanner = $this->createAdmin();

    $this->actingAs($scanner)
        ->postJson(route('dashboard.events.check-in.scan', $event), [
            'booking_reference' => $ticket->booking_reference,
        ])
        ->assertSuccessful();

    expect($ticket->fresh()->status)->toBe(TicketStatus::Used)
        ->and($ticket->fresh()->checked_in_at)->not->toBeNull()
        ->and($ticket->fresh()->checked_in_by)->toBe($scanner->id);
});

it('logs activity when a ticket is checked in', function (): void {
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $customer = $this->createUser();
    $order = $this->createPaidOrder($customer, $event, $ticketType);
    $ticket = $order->tickets()->first();

    $this->actingAs($this->createAdmin())
        ->postJson(route('dashboard.events.check-in.scan', $event), [
            'booking_reference' => $ticket->booking_reference,
        ]);

    $this->assertDatabaseHas('activity_logs', ['event' => 'ticket.checked_in']);
});

it('returns 422 for an already-used ticket', function (): void {
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $customer = $this->createUser();
    $order = $this->createPaidOrder($customer, $event, $ticketType);
    $ticket = $order->tickets()->first();
    $ticket->update(['status' => TicketStatus::Used]);

    $this->actingAs($this->createAdmin())
        ->postJson(route('dashboard.events.check-in.scan', $event), [
            'booking_reference' => $ticket->booking_reference,
        ])
        ->assertStatus(422);
});

it('returns 422 for a cancelled ticket', function (): void {
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $customer = $this->createUser();
    $order = $this->createPaidOrder($customer, $event, $ticketType);
    $ticket = $order->tickets()->first();
    $ticket->update(['status' => TicketStatus::Cancelled]);

    $this->actingAs($this->createAdmin())
        ->postJson(route('dashboard.events.check-in.scan', $event), [
            'booking_reference' => $ticket->booking_reference,
        ])
        ->assertStatus(422);
});

it('returns 404 for an invalid booking reference', function (): void {
    $event = $this->createPublishedEvent();

    $this->actingAs($this->createAdmin())
        ->postJson(route('dashboard.events.check-in.scan', $event), [
            'booking_reference' => 'EVT-INVALID',
        ])
        ->assertNotFound();
});
