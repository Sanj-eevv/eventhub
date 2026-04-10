<?php

declare(strict_types=1);

use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('shows the checkout page for the owner of a reserved order', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);

    $this->actingAs($user)
        ->get(route('checkout.show', ['order' => $order->uuid]))
        ->assertSuccessful()
        ->assertInertia(
            fn ($page) => $page
                ->component('Checkout/Show')
                ->has('client_secret')
                ->has('stripe_publishable_key')
        );
});

it('forbids another user from viewing the checkout', function (): void {
    $owner = $this->createUser();
    $other = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($owner, $event);

    $this->actingAs($other)
        ->get(route('checkout.show', ['order' => $order->uuid]))
        ->assertForbidden();
});

it('redirects away from checkout when the order is not in Reserved status', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->get(route('checkout.show', ['order' => $order->uuid]))
        ->assertRedirect();
});

it('cancels a reserved order and redirects with a success toast', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);

    $this->actingAs($user)
        ->delete(route('checkout.cancel', ['order' => $order->uuid]))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
});

it('forbids another user from cancelling a reserved order', function (): void {
    $owner = $this->createUser();
    $other = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($owner, $event);

    $this->actingAs($other)
        ->delete(route('checkout.cancel', ['order' => $order->uuid]))
        ->assertForbidden();
});
