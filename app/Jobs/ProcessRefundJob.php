<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ProcessRefundAction;
use App\Enums\RefundStatus;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Throwable;

#[Backoff(60)]
#[Tries(3)]
final class ProcessRefundJob implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public readonly Order $order,
        public readonly ?int $refundAmount = null,
    ) {}

    public function handle(ProcessRefundAction $action): void
    {
        $action($this->order, refundAmount: $this->refundAmount);
    }

    public function failed(Throwable $exception): void
    {
        $this->order->update(['refund_status' => RefundStatus::Failed]);

        logger()->error('ProcessRefundJob failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
