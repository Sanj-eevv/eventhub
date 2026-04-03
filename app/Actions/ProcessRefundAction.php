<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\PaymentGateway;
use App\Enums\RefundStatus;
use App\Models\Order;
use App\Services\SettingsService;
use Carbon\CarbonImmutable;

final class ProcessRefundAction
{
    public function __construct(
        private readonly PaymentGateway $paymentGateway,
        private readonly SettingsService $settingsService,
    ) {}

    public function execute(Order $order): void
    {
        $refundPercentage = $this->settingsService->get()->refundPercentage;
        $refundAmount = (int) round($order->total * $refundPercentage / 100);

        $refundId = $this->paymentGateway->refundPaymentIntent(
            $order->stripe_payment_intent_id,
            $refundAmount,
        );

        $order->update([
            'stripe_refund_id' => $refundId,
            'refund_status' => RefundStatus::Refunded,
            'refunded_at' => CarbonImmutable::now(),
        ]);
    }
}
