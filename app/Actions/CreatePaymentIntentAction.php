<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\PaymentGateway;
use App\DataTransferObjects\PaymentIntentData;
use App\DataTransferObjects\PaymentIntentResult;
use App\Models\Order;

final readonly class CreatePaymentIntentAction
{
    public function __construct(private PaymentGateway $paymentGateway) {}

    public function execute(Order $order): PaymentIntentResult
    {
        $result = $this->paymentGateway->createPaymentIntent(new PaymentIntentData(
            amount: $order->total,
            currency: $order->currency,
            order_uuid: $order->uuid,
            user_id: $order->user_id,
        ));

        $order->update([
            'stripe_payment_intent_id' => $result->payment_intent_id,
            'stripe_client_secret' => $result->client_secret,
        ]);

        return $result;
    }
}
