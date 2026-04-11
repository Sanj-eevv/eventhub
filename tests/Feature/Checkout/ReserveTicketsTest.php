<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Jobs\ExpireOrderJob;
use App\Models\Order;
use App\Models\TicketType;
use Illuminate\Support\Facades\Queue;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('prevents super admin from reserving tickets', function (): void {
    $user = $this->createSuperAdmin();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ])->assertForbidden();
});

it('prevents admin from reserving tickets', function (): void {
    $user = $this->createAdmin();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ])->assertForbidden();
});

it('prevents organization admin from reserving tickets', function (): void {
    $user = $this->createOrganizationAdmin();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ])->assertForbidden();
});

it('redirects guests to login', function (): void {
    [$event] = $this->createPublishedEventWithTicketType();

    $this->post(route('tickets.reserve', ['event' => $event->slug]))
        ->assertRedirect(route('auth.login'));
});

it('reserves tickets and creates a Reserved order with Pending tickets', function (): void {
    Queue::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 2]],
    ])->assertRedirect();

    $order = Order::query()->where('user_id', $user->id)->firstOrFail();

    expect($order->status)->toBe(OrderStatus::Reserved)
        ->and($order->tickets()->where('status', TicketStatus::Pending)->count())->toBe(2);
});

it('dispatches an ExpireOrderJob after reserving', function (): void {
    Queue::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ]);

    Queue::assertPushed(ExpireOrderJob::class);
});

it('sets the order expiry based on the settings reservation window', function (): void {
    Queue::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ]);

    $order = Order::query()->where('user_id', $user->id)->firstOrFail();

    expect($order->expires_at->isBetween(now()->addMinutes(4), now()->addMinutes(16)))->toBeTrue();
});

it('rejects reservation for a draft event', function (): void {
    $user = $this->createUser();
    $event = $this->createDraftEvent();
    $ticketType = TicketType::factory()->create(['event_id' => $event->id]);

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ])->assertForbidden();
});

it('rejects reservation when user already has an active reservation for the event', function (): void {
    Queue::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();

    $this->createReservedOrder($user, $event, $ticketType);

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ])->assertRedirect()->assertSessionHas('toast_error');
});

it('rejects reservation when capacity is insufficient', function (): void {
    $user = $this->createUser();
    $event = $this->createPublishedEvent();
    $ticketType = TicketType::factory()->create([
        'event_id' => $event->id,
        'capacity' => 1,
        'sale_starts_at' => now()->subDay(),
        'sale_ends_at' => now()->addMonth(),
    ]);

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 2]],
    ])->assertRedirect()->assertSessionHas('toast_error');
});

it('rejects reservation when user exceeds max_per_user limit', function (): void {
    Queue::fake();

    $user = $this->createUser();
    $event = $this->createPublishedEvent();
    $ticketType = TicketType::factory()->create([
        'event_id' => $event->id,
        'capacity' => 100,
        'max_per_user' => 1,
        'sale_starts_at' => now()->subDay(),
        'sale_ends_at' => now()->addMonth(),
    ]);

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 2]],
    ])->assertRedirect()->assertSessionHas('toast_error');
});

it('rejects reservation when ticket sale has not started yet', function (): void {
    $user = $this->createUser();
    $event = $this->createPublishedEvent();
    $ticketType = TicketType::factory()->create([
        'event_id' => $event->id,
        'sale_starts_at' => now()->addWeek(),
        'sale_ends_at' => now()->addMonth(),
    ]);

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ])->assertRedirect()->assertSessionHas('toast_error');
});

it('rejects reservation when ticket sale has closed', function (): void {
    $user = $this->createUser();
    $event = $this->createPublishedEvent();
    $ticketType = TicketType::factory()->create([
        'event_id' => $event->id,
        'sale_starts_at' => now()->subMonth(),
        'sale_ends_at' => now()->subDay(),
    ]);

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 1]],
    ])->assertRedirect()->assertSessionHas('toast_error');
});

it('fails validation when items array is missing', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [])
        ->assertSessionHasErrors('items');
});

it('fails validation when quantity is less than 1', function (): void {
    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();

    $this->actingAs($user)->post(route('tickets.reserve', ['event' => $event->slug]), [
        'items' => [['ticket_type_uuid' => $ticketType->uuid, 'quantity' => 0]],
    ])->assertSessionHasErrors('items.0.quantity');
});
