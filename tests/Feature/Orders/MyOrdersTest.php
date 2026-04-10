<?php

declare(strict_types=1);

use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('redirects guests to login', function (): void {
    $this->get(route('orders.index'))->assertRedirect(route('auth.login'));
});

it('shows the authenticated user their own orders', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->get(route('orders.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->has('orders'));
});

it('does not show another users orders in the index', function (): void {
    $user = $this->createUser();
    $other = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $otherOrder = $this->createPaidOrder($other, $event);

    $this->actingAs($user)
        ->get(route('orders.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn ($page) => $page
                ->where('orders.data', fn ($data) => collect($data)->pluck('uuid')->doesntContain($otherOrder->uuid))
        );
});

it('shows the authenticated user their own order detail', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->get(route('orders.show', ['order' => $order->uuid]))
        ->assertSuccessful();
});

it('forbids access to another users order detail', function (): void {
    $user = $this->createUser();
    $other = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $otherOrder = $this->createPaidOrder($other, $event);

    $this->actingAs($user)
        ->get(route('orders.show', ['order' => $otherOrder->uuid]))
        ->assertForbidden();
});
