<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\GenerateTicketQrCodesAction;
use App\Actions\RecordActivityAction;
use App\Enums\ActivityEvent;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

final class GenerateTicketQrCodesJob implements ShouldQueueAfterCommit
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public readonly Order $order) {}

    public function handle(GenerateTicketQrCodesAction $action): void
    {
        $action->execute($this->order);
    }

    public function failed(Throwable $exception, RecordActivityAction $recordActivityAction): void
    {
        $recordActivityAction->execute(ActivityEvent::QrCodeGenerationFailed, $this->order, properties: [
            'error' => $exception->getMessage(),
        ]);
    }
}
