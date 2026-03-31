<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Filesystem\FilesystemManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

final class GenerateTicketQrCodesAction
{
    public function __construct(private readonly FilesystemManager $filesystemManager) {}

    public function execute(Order $order): void
    {
        $order->loadMissing('tickets');

        $order->tickets->each(function (Ticket $ticket) use ($order): void {
            $svg = QrCode::format('svg')->generate($ticket->booking_reference);
            $path = "tickets/{$order->uuid}/{$ticket->uuid}.svg";

            $this->filesystemManager->disk('local')->put($path, $svg);

            $ticket->update(['qr_code_path' => $path]);
        });
    }
}
