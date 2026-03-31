<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\GenerateTicketQrCodesAction;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class GenerateTicketQrCodesJob implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public readonly Order $order) {}

    public function handle(GenerateTicketQrCodesAction $action): void
    {
        $action->execute($this->order);
    }
}
