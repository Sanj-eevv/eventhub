<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\GenerateTicketQrCodesAction;
use App\Actions\RecordActivityAction;
use App\Enums\ActivityEvent;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Throwable;

#[Backoff(30)]
#[Tries(3)]
final class GenerateTicketQrCodesJob implements ShouldQueueAfterCommit
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public readonly Order $order) {}

    public function handle(GenerateTicketQrCodesAction $action): void
    {
        $action($this->order);
    }

    public function failed(Throwable $exception, RecordActivityAction $recordActivityAction): void
    {
        $recordActivityAction(ActivityEvent::QrCodeGenerationFailed, $this->order, properties: [
            'error' => $exception->getMessage(),
        ]);
    }
}
