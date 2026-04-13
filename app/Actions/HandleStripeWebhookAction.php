<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Models\Order;
use App\Notifications\PaymentFailedNotification;
use Stripe\Event;

final readonly class HandleStripeWebhookAction
{
    public function __construct(
        private CompleteOrderAction $completeOrderAction,
        private RecordActivityAction $recordActivityAction,
    ) {}

    public function __invoke(Event $event): void
    {
        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded((string) $event->data->object->id),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed((string) $event->data->object->id),
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

        ($this->completeOrderAction)($order);
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

        ($this->recordActivityAction)(ActivityEvent::PaymentFailed, $order);
        $order->user->notify(new PaymentFailedNotification($order));
    }
}
