<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Inertia\Response;
use Inertia\ResponseFactory;

final class MyTicketController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function show(Ticket $ticket): Response
    {
        $this->authorize('view', $ticket);

        $ticket->load(['event', 'ticketType']);

        return $this->inertiaResponse->render('My/Tickets/Show', [
            'ticket' => TicketResource::make($ticket),
        ]);
    }
}
