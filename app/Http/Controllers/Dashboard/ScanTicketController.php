<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CheckInTicketAction;
use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ScanTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;

final class ScanTicketController extends Controller
{
    public function __construct(
        private readonly CheckInTicketAction $checkInTicketAction,
    ) {}

    public function __invoke(ScanTicketRequest $request, Event $event): JsonResponse
    {
        $ticket = Ticket::query()
            ->where('event_id', $event->id)
            ->where('booking_reference', $request->validated('booking_reference'))
            ->with(['event', 'ticketType'])
            ->first();

        if ( ! $ticket) {
            return response()->json(['error' => 'Ticket not found.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $ticket = $this->checkInTicketAction->execute($ticket, $request->user());
        } catch (InvalidStatusTransitionException $exception) {
            return response()->json(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (RuntimeException $exception) {
            return response()->json(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(TicketResource::make($ticket));
    }
}
