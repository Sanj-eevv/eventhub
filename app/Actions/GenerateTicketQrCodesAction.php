<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Filesystem\FilesystemManager;
use RuntimeException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

final readonly class GenerateTicketQrCodesAction
{
    public function __construct(private FilesystemManager $filesystemManager) {}

    public function __invoke(Order $order): void
    {
        $order->loadMissing('tickets');

        $order->tickets->each(function (Ticket $ticket) use ($order): void {
            $svg = QrCode::format('svg')->generate($ticket->booking_reference);
            $path = sprintf('tickets/%s/%s.svg', $order->uuid, $ticket->uuid);

            if ( ! $this->filesystemManager->disk('local')->put($path, $svg)) {
                throw new RuntimeException(sprintf('Failed to write QR code to disk for ticket [%s].', $ticket->uuid));
            }

            $ticket->update(['qr_code_path' => $path]);
        });
    }
}
