<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ExpireOrderAction;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;

final class ExpireOrderJob implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    public function handle(ExpireOrderAction $action): void
    {
        $action->execute($this->order);
    }
}
