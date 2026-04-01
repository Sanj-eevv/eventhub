<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Filesystem\FilesystemManager;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class DownloadOrderTicketsPdfController extends Controller
{
    public function __construct(private readonly FilesystemManager $filesystemManager) {}

    public function __invoke(Order $order): PdfBuilder
    {
        $this->authorize('view', $order);

        if (OrderStatus::Paid !== $order->status) {
            throw new AccessDeniedHttpException();
        }

        $order->load(['tickets.ticketType', 'event']);

        $svgContents = $order->tickets
            ->mapWithKeys(function (Ticket $ticket): array {
                $content = $ticket->qr_code_path
                    ? $this->filesystemManager->disk('local')->get($ticket->qr_code_path)
                    : null;

                return [$ticket->uuid => $content ?: null];
            });

        return Pdf::view('pdf.order-tickets', [
            'order' => $order,
            'svgContents' => $svgContents,
        ])->download("{$order->event->title}.pdf");
    }
}
