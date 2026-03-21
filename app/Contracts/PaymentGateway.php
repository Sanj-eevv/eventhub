<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DataTransferObjects\PaymentIntentData;
use App\DataTransferObjects\PaymentIntentResult;

interface PaymentGateway
{
    public function createPaymentIntent(PaymentIntentData $data): PaymentIntentResult;

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntentResult;

    public function cancelPaymentIntent(string $paymentIntentId): void;
}
