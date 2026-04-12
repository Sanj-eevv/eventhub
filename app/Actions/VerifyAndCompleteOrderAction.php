<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\PaymentGateway;
use App\Enums\OrderStatus;
use App\Models\Order;

final readonly class VerifyAndCompleteOrderAction
{
    public function __construct(
        private CompleteOrderAction $completeOrderAction,
        private PaymentGateway $paymentGateway,
    ) {}

    public function execute(Order $order): Order
    {
        if (OrderStatus::Reserved !== $order->status || null === $order->stripe_payment_intent_id) {
            return $order;
        }

        $result = $this->paymentGateway->retrievePaymentIntent($order->stripe_payment_intent_id);

        if ('succeeded' === $result->status) {
            return $this->completeOrderAction->execute($order);
        }

        return $order;
    }
}
