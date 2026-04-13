<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CheckInTicketAction;
use App\Enums\TicketStatus;
use App\Events\DuplicateScanAttempted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ScanTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;

final class ScanTicketController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly CheckInTicketAction $checkInTicketAction,
        private readonly Dispatcher $dispatcher,
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
            $ticket = ($this->checkInTicketAction)($ticket, $this->authManager->user());
        } catch (RuntimeException) {
            if (TicketStatus::Used === $ticket->status) {
                $this->dispatcher->dispatch(new DuplicateScanAttempted($ticket));
            }

            return $this->responseFactory->json(['error' => 'This ticket cannot be checked in.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->responseFactory->json(TicketResource::make($ticket));
    }
}
