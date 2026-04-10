<?php

declare(strict_types=1);

use App\Actions\HandleStripeWebhookAction;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Jobs\GenerateTicketQrCodesJob;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\PaymentFailedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Stripe\Event as StripeEvent;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('returns 400 for a webhook with an invalid Stripe signature', function (): void {
    $this->postJson('/webhooks/stripe', [], ['Stripe-Signature' => 'invalid'])
        ->assertStatus(400);
});

it('completes a reserved order when payment_intent.succeeded is received', function (): void {
    Queue::fake();
    Notification::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_succeeded_123']);

    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_test_succeeded_123']],
    ]);

    app(HandleStripeWebhookAction::class)->execute($stripeEvent);

    expect($order->fresh()->status)->toBe(OrderStatus::Paid);
});

it('activates tickets when payment_intent.succeeded is received', function (): void {
    Queue::fake();
    Notification::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_activate_123']);

    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_test_activate_123']],
    ]);

    app(HandleStripeWebhookAction::class)->execute($stripeEvent);

    $ticket = $order->tickets()->first();

    expect($ticket->fresh()->status)->toBe(TicketStatus::Active);
});

it('sends an order confirmation notification when payment_intent.succeeded is received', function (): void {
    Queue::fake();
    Notification::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_notify_123']);

    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_test_notify_123']],
    ]);

    app(HandleStripeWebhookAction::class)->execute($stripeEvent);

    Notification::assertSentTo($user, OrderConfirmedNotification::class);
});

it('dispatches GenerateTicketQrCodesJob when payment_intent.succeeded is received', function (): void {
    Queue::fake();
    Notification::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_qr_123']);

    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_test_qr_123']],
    ]);

    app(HandleStripeWebhookAction::class)->execute($stripeEvent);

    Queue::assertPushed(GenerateTicketQrCodesJob::class);
});

it('silently ignores payment_intent.succeeded for an unknown payment intent', function (): void {
    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_unknown_xyz']],
    ]);

    expect(fn () => app(HandleStripeWebhookAction::class)->execute($stripeEvent))->not->toThrow(Throwable::class);
});

it('is idempotent when payment_intent.succeeded is received for an already paid order', function (): void {
    Queue::fake();
    Notification::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_idempotent_123']);

    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_test_idempotent_123']],
    ]);

    app(HandleStripeWebhookAction::class)->execute($stripeEvent);

    Notification::assertNothingSent();
});

it('records activity and notifies user when payment_intent.payment_failed is received', function (): void {
    Notification::fake();

    $user = $this->createUser();
    [$event] = $this->createPublishedEventWithTicketType();
    $order = $this->createReservedOrder($user, $event);
    $order->update(['stripe_payment_intent_id' => 'pi_test_failed_123']);

    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'payment_intent.payment_failed',
        'data' => ['object' => ['id' => 'pi_test_failed_123']],
    ]);

    app(HandleStripeWebhookAction::class)->execute($stripeEvent);

    Notification::assertSentTo($user, PaymentFailedNotification::class);
    $this->assertDatabaseHas('activity_logs', ['event' => 'order.payment_failed']);
});

it('silently ignores payment_intent.payment_failed for an unknown payment intent', function (): void {
    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'payment_intent.payment_failed',
        'data' => ['object' => ['id' => 'pi_unknown_abc']],
    ]);

    expect(fn () => app(HandleStripeWebhookAction::class)->execute($stripeEvent))->not->toThrow(Throwable::class);
});

it('silently ignores unknown webhook event types', function (): void {
    $stripeEvent = StripeEvent::constructFrom([
        'id' => 'evt_test',
        'type' => 'customer.created',
        'data' => ['object' => ['id' => 'cus_test']],
    ]);

    expect(fn () => app(HandleStripeWebhookAction::class)->execute($stripeEvent))->not->toThrow(Throwable::class);
});
