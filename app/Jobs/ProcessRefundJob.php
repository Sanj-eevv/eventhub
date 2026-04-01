<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ProcessRefundAction;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class ProcessRefundJob implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public readonly Order $order) {}

    public function handle(ProcessRefundAction $action): void
    {
        $action->execute($this->order);
    }
}
