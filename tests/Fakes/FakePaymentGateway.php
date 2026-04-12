<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Contracts\PaymentGateway;
use App\DataTransferObjects\PaymentIntentData;
use App\DataTransferObjects\PaymentIntentResult;

final class FakePaymentGateway implements PaymentGateway
{
    private string $retrieveStatus = 'succeeded';

    /** @var array<int, array<string, mixed>> */
    private array $createdIntents = [];

    /** @var array<int, array<string, mixed>> */
    private array $refundedIntents = [];

    public function failOnNextCall(): self
    {
        return $this;
    }

    public function withRetrieveStatus(string $status): self
    {
        $this->retrieveStatus = $status;

        return $this;
    }

    public function createPaymentIntent(PaymentIntentData $data): PaymentIntentResult
    {
        $this->createdIntents[] = ['data' => $data];

        return new PaymentIntentResult(
            payment_intent_id: 'pi_fake_'.uniqid(),
            client_secret: 'pi_fake_secret_'.uniqid(),
            status: 'requires_payment_method',
        );
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntentResult
    {
        return new PaymentIntentResult(
            payment_intent_id: $paymentIntentId,
            client_secret: 'pi_fake_secret',
            status: $this->retrieveStatus,
        );
    }

    public function cancelPaymentIntent(string $paymentIntentId): void {}

    public function refundPaymentIntent(string $paymentIntentId, int $amount): string
    {
        $this->refundedIntents[] = ['payment_intent_id' => $paymentIntentId, 'amount' => $amount];

        return 're_fake_'.uniqid();
    }

    public function assertPaymentIntentCreated(): void
    {
        expect($this->createdIntents)->not->toBeEmpty('Expected a payment intent to be created but none was.');
    }

    public function assertRefunded(string $paymentIntentId): void
    {
        $refund = collect($this->refundedIntents)
            ->firstWhere('payment_intent_id', $paymentIntentId);

        expect($refund)->not->toBeNull(sprintf('Expected refund for %s but none was issued.', $paymentIntentId));
    }

    public function getLastRefundAmount(): int
    {
        return (int) collect($this->refundedIntents)->last()['amount'];
    }

    public function getCreatedIntentsCount(): int
    {
        return count($this->createdIntents);
    }
}
