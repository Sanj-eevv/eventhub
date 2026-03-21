<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreTicketTypeRequest;
use App\Http\Requests\Dashboard\UpdateTicketTypeRequest;
use App\Http\Resources\TicketTypeResource;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class TicketTypeController extends Controller
{
    public function __construct(
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Event $event): Response
    {
        $this->authorize('viewAny', TicketType::class);

        $ticketTypes = $event->ticketTypes()->get();

        return $this->inertiaResponse->render('Dashboard/TicketTypes/Index', [
            'event' => $event,
            'ticketTypes' => TicketTypeResource::collection($ticketTypes),
        ]);
    }

    public function create(Event $event): Response
    {
        $this->authorize('create', TicketType::class);

        return $this->inertiaResponse->render('Dashboard/TicketTypes/Create', [
            'event' => $event,
        ]);
    }

    public function store(StoreTicketTypeRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('create', TicketType::class);

        $event->ticketTypes()->create($request->toDto());

        return $this->redirector->back()->with('toastSuccess', 'Ticket type created.');
    }

    public function edit(Event $event, TicketType $ticketType): Response
    {
        $this->authorize('update', TicketType::class);

        return $this->inertiaResponse->render('Dashboard/TicketTypes/Edit', [
            'event' => $event,
            'ticketType' => TicketTypeResource::make($ticketType),
        ]);
    }

    public function update(UpdateTicketTypeRequest $request, Event $event, TicketType $ticketType): RedirectResponse
    {
        $this->authorize('update', TicketType::class);

        $ticketType->update($request->toDto());

        return $this->redirector->back()->with('toastSuccess', 'Ticket type updated.');
    }

    public function destroy(Event $event, TicketType $ticketType): RedirectResponse
    {
        $this->authorize('delete', TicketType::class);

        $ticketType->delete();

        return $this->redirector->back()->with('toastSuccess', 'Ticket type deleted.');
    }
}
