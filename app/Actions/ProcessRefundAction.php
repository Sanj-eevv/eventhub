<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\PaymentGateway;
use App\Enums\RefundStatus;
use App\Models\Order;
use App\Models\Setting;
use Carbon\CarbonImmutable;

final class ProcessRefundAction
{
    public function __construct(private readonly PaymentGateway $paymentGateway) {}

    public function execute(Order $order): void
    {
        $refundPercentage = (int) Setting::get('refund_percentage', default: 100);
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
