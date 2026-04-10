<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Order;
use Stripe\Event;

final class HandleStripeWebhookAction
{
    public function __construct(
        private readonly CompleteOrderAction $completeOrderAction,
    ) {}

    public function execute(Event $event): void
    {
        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event->data->object->id),
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
}
