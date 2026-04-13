<?php

declare(strict_types=1);

namespace App\Payments;

use App\Contracts\PaymentGateway;
use App\DataTransferObjects\PaymentIntentData;
use App\DataTransferObjects\PaymentIntentResult;
use Stripe\StripeClient;

final readonly class StripePaymentGateway implements PaymentGateway
{
    public function __construct(private StripeClient $stripe) {}

    public function createPaymentIntent(PaymentIntentData $data): PaymentIntentResult
    {
        $intent = $this->stripe->paymentIntents->create([
            'amount' => $data->amount,
            'currency' => $data->currency,
            'metadata' => [
                'order_uuid' => $data->order_uuid,
                'user_id' => $data->user_id,
            ],
        ]);

        return new PaymentIntentResult(
            payment_intent_id: $intent->id,
            client_secret: (string) $intent->client_secret,
            status: $intent->status,
        );
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntentResult
    {
        $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

        return new PaymentIntentResult(
            payment_intent_id: $intent->id,
            client_secret: (string) $intent->client_secret,
            status: $intent->status,
        );
    }

    public function cancelPaymentIntent(string $paymentIntentId): void
    {
        $this->stripe->paymentIntents->cancel($paymentIntentId);
    }

    public function refundPaymentIntent(string $paymentIntentId, int $amount): string
    {
        $refund = $this->stripe->refunds->create([
            'payment_intent' => $paymentIntentId,
            'amount' => $amount,
        ]);

        return $refund->id;
    }
}
