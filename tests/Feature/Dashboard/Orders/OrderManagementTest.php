<?php

declare(strict_types=1);

use App\Jobs\ProcessRefundJob;
use App\Models\Organization;
use Illuminate\Support\Facades\Queue;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('allows admins to list all orders', function (): void {
    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.orders.index'))
        ->assertSuccessful();
});

it('forbids users without permission from listing orders', function (): void {
    $this->actingAs($this->createUser())
        ->get(route('dashboard.orders.index'))
        ->assertForbidden();
});

it('restricts organization admins to their organization orders', function (): void {
    $organization = Organization::factory()->approved()->create();
    $orgAdmin = $this->createOrganizationAdmin($organization);

    [$ownEvent, $ownTicketType] = $this->createPublishedEventWithTicketType($organization);
    $ownOrder = $this->createPaidOrder($this->createUser(), $ownEvent, $ownTicketType);

    [$otherEvent, $otherTicketType] = $this->createPublishedEventWithTicketType();
    $otherOrder = $this->createPaidOrder($this->createUser(), $otherEvent, $otherTicketType);

    $this->actingAs($orgAdmin)
        ->get(route('dashboard.orders.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn ($page) => $page
                ->where('orders.data', fn ($data): bool => collect($data)->pluck('uuid')->contains($ownOrder->uuid)
                    && collect($data)->pluck('uuid')->doesntContain($otherOrder->uuid))
        );
});

it('allows admins to view an order detail', function (): void {
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($this->createUser(), $event, $ticketType);

    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.orders.show', $order))
        ->assertSuccessful();
});

it('allows admins to cancel a paid order and dispatch a refund', function (): void {
    Queue::fake();

    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $event->update(['starts_at' => now()->addMonths(6)]);
    $order = $this->createPaidOrder($this->createUser(), $event, $ticketType);

    $this->actingAs($this->createAdmin())
        ->delete(route('dashboard.orders.cancel', $order->uuid))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    Queue::assertPushed(ProcessRefundJob::class);
});
