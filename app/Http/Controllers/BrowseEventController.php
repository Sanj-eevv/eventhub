<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Event\ShowResource;
use App\Http\Resources\TicketTypeResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
            ->with('media')
            ->paginate(12);

        return $this->inertiaResponse->render('Events/Index', [
            'events' => Inertia::scroll(ShowResource::collection($events)),
        ]);
    }

    public function show(Event $event): Response
    {
        abort_if('published' !== $event->status->value, 404);

        $event->load([
            'ticketTypes' => fn ($query) => $query->active()->orderBy('sort_order'),
            'media',
        ]);

        return $this->inertiaResponse->render('Events/Show', [
            'event' => ShowResource::make($event),
            'ticketTypes' => TicketTypeResource::collection($event->ticketTypes),
        ]);
    }
}
