<?php

declare(strict_types=1);

use App\Actions\ProcessRefundAction;
use App\Contracts\PaymentGateway;
use App\Enums\RefundStatus;
use App\Jobs\ProcessRefundJob;
use App\Notifications\RefundCompletedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\Fakes\FakePaymentGateway;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('calls the payment gateway to refund the payment intent', function (): void {
    Notification::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new ProcessRefundJob($order))->handle(resolve(ProcessRefundAction::class));

    /** @var FakePaymentGateway $gateway */
    $gateway = resolve(PaymentGateway::class);
    $gateway->assertRefunded($order->stripe_payment_intent_id);
});

it('sets the refund_status to Refunded after processing', function (): void {
    Notification::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new ProcessRefundJob($order))->handle(resolve(ProcessRefundAction::class));

    expect($order->fresh()->refund_status)->toBe(RefundStatus::Refunded);
});

it('stores the stripe refund id on the order', function (): void {
    Notification::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new ProcessRefundJob($order))->handle(resolve(ProcessRefundAction::class));

    expect($order->fresh()->stripe_refund_id)->toStartWith('re_fake_');
});

it('sends a RefundCompletedNotification to the order owner', function (): void {
    Notification::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new ProcessRefundJob($order))->handle(resolve(ProcessRefundAction::class));

    Notification::assertSentTo($user, RefundCompletedNotification::class);
});

it('logs a RefundProcessed activity', function (): void {
    Notification::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new ProcessRefundJob($order))->handle(resolve(ProcessRefundAction::class));

    $this->assertDatabaseHas('activity_logs', ['event' => 'refund.processed']);
});

it('uses the settings refund percentage when no explicit amount is given', function (): void {
    Notification::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new ProcessRefundJob($order))->handle(resolve(ProcessRefundAction::class));

    /** @var FakePaymentGateway $gateway */
    $gateway = resolve(PaymentGateway::class);

    expect($gateway->getLastRefundAmount())->toBe($order->total);
});

it('uses an explicit refund amount when provided', function (): void {
    Notification::fake();

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new ProcessRefundJob($order, 500))->handle(resolve(ProcessRefundAction::class));

    /** @var FakePaymentGateway $gateway */
    $gateway = resolve(PaymentGateway::class);

    expect($gateway->getLastRefundAmount())->toBe(500);
});

it('marks refund_status as Failed when the job fails', function (): void {
    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    $job = new ProcessRefundJob($order);
    $job->failed(new RuntimeException('Gateway error'));

    expect($order->fresh()->refund_status)->toBe(RefundStatus::Failed);
});
