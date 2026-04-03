<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CheckInTicketAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ScanTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;

final class ScanTicketController extends Controller
{
    public function __construct(
        private readonly CheckInTicketAction $checkInTicketAction,
        private readonly ResponseFactory $responseFactory,
    ) {}

    public function __invoke(ScanTicketRequest $request, Event $event): JsonResponse
    {
        $this->authorize('checkIn', $event);

        $ticket = Ticket::query()
            ->forEvent($event)
            ->byBookingReference($request->validated('booking_reference'))
            ->with(['event', 'ticketType'])
            ->first();

        if ( ! $ticket) {
            return $this->responseFactory->json(['error' => 'Ticket not found.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $ticket = $this->checkInTicketAction->execute($ticket, $request->user());
        } catch (RuntimeException) {
            return $this->responseFactory->json(['error' => 'This ticket cannot be checked in.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->responseFactory->json(TicketResource::make($ticket));
    }
}
