<?php

declare(strict_types=1);

use App\Contracts\PaymentGateway;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Events\OrderCompleted;
use App\Events\OrderStatusChanged;
use Illuminate\Support\Facades\Event;
use Tests\Fakes\FakePaymentGateway;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('completes the order and redirects to confirmation when payment intent succeeded', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_123']);

    $this->actingAs($user)
        ->post(route('checkout.pay', ['order' => $order->uuid]))
        ->assertRedirect(route('checkout.confirmation', ['order' => $order->uuid]));

    expect($order->fresh()->status)->toBe(OrderStatus::Paid);
    expect($order->fresh()->tickets->every(fn ($ticket): bool => TicketStatus::Active === $ticket->status))->toBeTrue();
});

it('does not complete the order when payment intent has not succeeded', function (): void {
    /** @var FakePaymentGateway $gateway */
    $gateway = $this->app->make(PaymentGateway::class);
    $gateway->withRetrieveStatus('requires_payment_method');

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_456']);

    $this->actingAs($user)
        ->post(route('checkout.pay', ['order' => $order->uuid]))
        ->assertRedirect(route('checkout.confirmation', ['order' => $order->uuid]));

    expect($order->fresh()->status)->toBe(OrderStatus::Reserved);
});

it('skips verification and redirects when order is already paid', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event);

    $this->actingAs($user)
        ->post(route('checkout.pay', ['order' => $order->uuid]))
        ->assertRedirect(route('checkout.confirmation', ['order' => $order->uuid]));

    expect($order->fresh()->status)->toBe(OrderStatus::Paid);
});

it('skips verification when order has no stripe payment intent id', function (): void {
    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);

    $this->actingAs($user)
        ->post(route('checkout.pay', ['order' => $order->uuid]))
        ->assertRedirect(route('checkout.confirmation', ['order' => $order->uuid]));

    expect($order->fresh()->status)->toBe(OrderStatus::Reserved);
});

it('forbids another user from processing payment', function (): void {
    $owner = $this->createUser();
    $other = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($owner, $event);

    $this->actingAs($other)
        ->post(route('checkout.pay', ['order' => $order->uuid]))
        ->assertForbidden();
});

it('redirects guests to login', function (): void {
    [$event] = $this->createPublishedEventWithTicketType();
    $owner = $this->createUser();
    $order = $this->createReservedOrder($owner, $event);

    $this->post(route('checkout.pay', ['order' => $order->uuid]))
        ->assertRedirect(route('auth.login'));
});

it('broadcasts OrderCompleted when payment succeeds', function (): void {
    Event::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_123']);

    $this->actingAs($user)
        ->post(route('checkout.pay', ['order' => $order->uuid]));

    Event::assertDispatched(OrderCompleted::class, fn (OrderCompleted $e): bool => $e->order->is($order));
});

it('broadcasts OrderStatusChanged when order is paid', function (): void {
    Event::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_123']);

    $this->actingAs($user)
        ->post(route('checkout.pay', ['order' => $order->uuid]));

    Event::assertDispatched(OrderStatusChanged::class, fn (OrderStatusChanged $e): bool => $e->order->is($order) && 'paid' === $e->order->status->value);
});
