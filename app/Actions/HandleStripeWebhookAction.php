<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Models\Order;
use App\Notifications\PaymentFailedNotification;
use Stripe\Event;

final class HandleStripeWebhookAction
{
    public function __construct(
        private readonly CompleteOrderAction $completeOrderAction,
        private readonly RecordActivityAction $recordActivityAction,
    ) {}

    public function execute(Event $event): void
    {
        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event->data->object->id),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event->data->object->id),
            default => null,
        };
    }

    private function handlePaymentIntentSucceeded(string $paymentIntentId): void
    {
        $order = Order::query()
            ->where('stripe_payment_intent_id', $paymentIntentId)
            ->first();

        if (null === $order) {
            return;
        }

        $this->completeOrderAction->execute($order);
    }

    private function handlePaymentIntentFailed(string $paymentIntentId): void
    {
        $order = Order::query()
            ->with(['user', 'event'])
            ->where('stripe_payment_intent_id', $paymentIntentId)
            ->first();

        if (null === $order) {
            return;
        }

        $this->recordActivityAction->execute(ActivityEvent::PaymentFailed, $order);
        $order->user->notify(new PaymentFailedNotification($order));
    }
}
