<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class PaymentIntentResult
{
    public function __construct(
        public string $payment_intent_id,
        public string $client_secret,
        public string $status,
    ) {}
}
