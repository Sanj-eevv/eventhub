<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Event\ShowResource;
use App\Http\Resources\TicketTypeResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

final class BrowseEventController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): Response
    {
        $events = Event::query()
            ->published()
            ->paginate(perPage: $request->integer('per_page', 12), page: $request->integer('page', 1));

        return $this->inertiaResponse->render('Events/Index', [
            'events' => ShowResource::collection($events),
        ]);
    }

    public function show(Event $event): Response
    {
        abort_if('published' !== $event->status->value, 404);

        $event->load([
            'ticketTypes' => fn ($query) => $query->active()->orderBy('sort_order'),
        ]);

        return $this->inertiaResponse->render('Events/Show', [
            'event' => ShowResource::make($event),
            'ticketTypes' => TicketTypeResource::collection($event->ticketTypes),
        ]);
    }
}
