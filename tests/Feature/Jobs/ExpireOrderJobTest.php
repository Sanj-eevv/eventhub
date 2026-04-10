<?php

declare(strict_types=1);

use App\Actions\ExpireOrderAction;
use App\Enums\OrderStatus;
use App\Jobs\ExpireOrderJob;
use App\Models\Order;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('deletes a reserved order and its tickets when handled', function (): void {
    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event, $ticketType);

    (new ExpireOrderJob($order))->handle(app(ExpireOrderAction::class));

    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    $this->assertDatabaseMissing('tickets', ['order_id' => $order->id]);
});

it('does nothing when the order is already paid', function (): void {
    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new ExpireOrderJob($order))->handle(app(ExpireOrderAction::class));

    $this->assertDatabaseHas('orders', ['id' => $order->id]);
});

it('does nothing when the order is already cancelled', function (): void {
    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);
    $order->update(['status' => OrderStatus::Cancelled]);

    (new ExpireOrderJob($order))->handle(app(ExpireOrderAction::class));

    $this->assertDatabaseHas('orders', ['id' => $order->id]);
});

it('has deleteWhenMissingModels set to true', function (): void {
    $job = new ExpireOrderJob(Order::factory()->make());

    expect($job->deleteWhenMissingModels)->toBeTrue();
});
