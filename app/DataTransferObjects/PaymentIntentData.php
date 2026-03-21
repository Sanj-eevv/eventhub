<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class PaymentIntentData
{
    public function __construct(
        public int $amount,
        public string $currency,
        public string $order_uuid,
        public int $user_id,
    ) {}
}
