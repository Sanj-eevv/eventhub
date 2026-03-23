<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Http\Resources\EventResource;
use App\Http\Resources\TicketTypeResource;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BrowseEventController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(): Response
    {
        $events = Event::query()
            ->published()
            ->orderBy('starts_at', 'desc')
            ->with('coverImage')
            ->paginate(12);

        return $this->inertiaResponse->render('Events/Index', [
            'events' => Inertia::scroll(EventResource::collection($events)),
        ]);
    }

    public function show(Event $event): Response
    {
        if (EventStatus::Published !== $event->status) {
            throw new NotFoundHttpException();
        }

        $event->load([
            'ticketTypes' => fn ($query) => $query->active()->orderBy('sort_order'),
            'media',
            'coverImage',
        ]);

        return $this->inertiaResponse->render('Events/Show', [
            'event' => EventResource::make($event),
            'ticketTypes' => TicketTypeResource::collection($event->ticketTypes->each->setRelation('event', $event)),
        ]);
    }
}
