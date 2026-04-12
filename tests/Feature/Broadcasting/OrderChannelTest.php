<?php

declare(strict_types=1);

use App\Models\Order;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('allows the order owner to subscribe to the order channel', function (): void {
    $owner = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($owner, $event);

    $canSubscribe = Order::query()
        ->where('uuid', $order->uuid)
        ->where('user_id', $owner->id)
        ->exists();

    expect($canSubscribe)->toBeTrue();
});

it('denies another user from subscribing to the order channel', function (): void {
    $owner = $this->createUser();
    $other = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($owner, $event);

    $canSubscribe = Order::query()
        ->where('uuid', $order->uuid)
        ->where('user_id', $other->id)
        ->exists();

    expect($canSubscribe)->toBeFalse();
});
