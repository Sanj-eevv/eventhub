<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Filesystem\FilesystemManager;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TicketQrCodeController extends Controller
{
    public function __construct(private readonly FilesystemManager $filesystemManager) {}

    public function __invoke(Ticket $ticket): StreamedResponse
    {
        $this->authorize('view', $ticket);

        if ( ! $ticket->qr_code_path) {
            throw new NotFoundHttpException();
        }

        return $this->filesystemManager->disk('local')->response($ticket->qr_code_path, headers: [
            'Content-Type' => 'image/svg+xml',
        ]);
    }
}
