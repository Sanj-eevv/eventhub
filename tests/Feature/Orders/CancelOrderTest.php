<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Events\OrderCancelled;
use App\Jobs\ProcessRefundJob;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('cancels a reserved order and removes it from the database', function (): void {
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

it('cancels a paid order and dispatches a refund job', function (): void {
    Queue::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();

    $event->update(['starts_at' => now()->addMonths(6)]);

    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->delete(route('orders.cancel', ['order' => $order->uuid]))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);

    Queue::assertPushed(ProcessRefundJob::class);
});

it('cancels active tickets when a paid order is cancelled', function (): void {
    Queue::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();

    $event->update(['starts_at' => now()->addMonths(6)]);

    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->delete(route('orders.cancel', ['order' => $order->uuid]));

    $ticket = $order->tickets()->first();

    expect($ticket->fresh()->status)->toBe(TicketStatus::Cancelled);
});

it('blocks cancellation of a paid order within the cutoff window', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();

    $event->update(['starts_at' => now()->addHours(12)]);

    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->delete(route('orders.cancel', ['order' => $order->uuid]))
        ->assertStatus(422);
});

it('forbids another user from cancelling a paid order', function (): void {
    $owner = $this->createUser();
    $other = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();

    $event->update(['starts_at' => now()->addMonths(6)]);

    $order = $this->createPaidOrder($owner, $event);

    $this->actingAs($other)
        ->delete(route('orders.cancel', ['order' => $order->uuid]))
        ->assertForbidden();
});

it('deletes the QR code directory when a paid order is cancelled', function (): void {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();

    $event->update(['starts_at' => now()->addMonths(6)]);

    $order = $this->createPaidOrder($user, $event);
    Storage::disk('local')->makeDirectory('tickets/'.$order->uuid);
    Storage::disk('local')->put(sprintf('tickets/%s/ticket.svg', $order->uuid), 'fake-qr-content');

    $this->actingAs($user)
        ->delete(route('orders.cancel', ['order' => $order->uuid]));

    Storage::disk('local')->assertMissing(sprintf('tickets/%s/ticket.svg', $order->uuid));
});

it('broadcasts OrderCancelled when a paid order is cancelled', function (): void {
    Event::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $event->update(['starts_at' => now()->addMonths(6)]);
    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->delete(route('orders.cancel', ['order' => $order->uuid]));

    Event::assertDispatched(OrderCancelled::class, fn (OrderCancelled $e): bool => $e->order->is($order));
});
