<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('shows the confirmation page for the owner of a paid order', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->get(route('checkout.confirmation', ['order' => $order->uuid]))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Checkout/Confirmation')->has('order'));
});

it('shows the confirmation page for the owner of a reserved order', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);

    $this->actingAs($user)
        ->get(route('checkout.confirmation', ['order' => $order->uuid]))
        ->assertSuccessful();
});

it('forbids another user from viewing the confirmation', function (): void {
    $owner = $this->createUser();
    $other = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($owner, $event);

    $this->actingAs($other)
        ->get(route('checkout.confirmation', ['order' => $order->uuid]))
        ->assertForbidden();
});

it('returns 404 for a cancelled order confirmation', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event);
    $order->update(['status' => OrderStatus::Cancelled]);

    $this->actingAs($user)
        ->get(route('checkout.confirmation', ['order' => $order->uuid]))
        ->assertNotFound();
});
