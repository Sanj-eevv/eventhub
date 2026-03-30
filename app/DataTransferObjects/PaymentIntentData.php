<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Support\Str;

final readonly class PaymentIntentData
{
    public string $currency;

    public function __construct(
        public int $amount,
        string $currency,
        public string $order_uuid,
        public int $user_id,
    ) {
        $this->currency = Str::lower($currency);
    }
}
