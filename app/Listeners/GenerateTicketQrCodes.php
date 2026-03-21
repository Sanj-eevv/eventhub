<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

final class GenerateTicketQrCodes implements ShouldQueue
{
    public function handle(OrderCompleted $event): void
    {
        $order = $event->order->loadMissing('tickets');

        $order->tickets->each(function (Ticket $ticket) use ($order): void {
            $svg = QrCode::format('svg')->generate($ticket->booking_reference);
            $path = "tickets/{$order->uuid}/{$ticket->uuid}.svg";

            Storage::put($path, $svg);

            $ticket->update(['qr_code_path' => $path]);
        });
    }
}
