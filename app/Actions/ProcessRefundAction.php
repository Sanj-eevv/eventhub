<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\PaymentGateway;
use App\Enums\ActivityEvent;
use App\Enums\RefundStatus;
use App\Models\Order;
use App\Models\User;
use App\Notifications\RefundCompletedNotification;
use App\Services\SettingsService;
use Carbon\CarbonImmutable;

final readonly class ProcessRefundAction
{
    public function __construct(
        private PaymentGateway $paymentGateway,
        private SettingsService $settingsService,
        private RecordActivityAction $recordActivityAction,
    ) {}

    public function __invoke(Order $order, ?User $causer = null, ?int $refundAmount = null): void
    {
        $refundAmount ??= $this->settingsService->get()->refundPercentage->applyTo($order->total);

        $refundId = $this->paymentGateway->refundPaymentIntent(
            $order->stripe_payment_intent_id,
            $refundAmount,
        );

        $order->update([
            'stripe_refund_id' => $refundId,
            'refund_status' => RefundStatus::Refunded,
            'refunded_at' => CarbonImmutable::now(),
        ]);

        ($this->recordActivityAction)(ActivityEvent::RefundProcessed, $order, $causer, [
            'refund_id' => $refundId,
            'amount' => $refundAmount,
        ]);

        $order->loadMissing(['user', 'event']);

        $order->user->notify(new RefundCompletedNotification($order, $refundAmount));
    }
}
