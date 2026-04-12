<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Enums\TicketStatus;
use App\Http\Requests\BrowseEventsRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Auth\AuthManager;
use Inertia\Response;
use Inertia\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BrowseEventController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(BrowseEventsRequest $request): Response
    {
        $search = $request->string('search')->toString() ?: null;
        $upcoming = $request->boolean('upcoming');

        $events = Event::query()
            ->published()
            ->search($search)
            ->when($upcoming, fn ($query) => $query->upcoming())
            ->orderBy('starts_at', 'asc')
            ->with('coverImage')
            ->paginate(12);

        return $this->inertiaResponse->render('Events/Index', [
            'events' => $this->inertiaResponse->scroll(EventResource::collection($events)),
            'filters' => [
                'search' => $search ?? '',
                'upcoming' => $upcoming,
            ],
        ]);
    }

    public function show(Event $event): Response
    {
        throw_if(EventStatus::Published !== $event->status, NotFoundHttpException::class);

        $user = $this->authManager->user();

        $event->load([
            'ticketTypes' => fn ($query) => $query
                ->withCount([
                    'tickets' => fn ($query) => $query->where('status', '!=', TicketStatus::Cancelled),
                    ...($user ? ['tickets as user_tickets_count' => fn ($query) => $query
                        ->where('user_id', $user->id)
                        ->whereIn('status', [TicketStatus::Pending, TicketStatus::Active]),
                    ] : []),
                ]),
            'media',
            'coverImage',
        ]);

        $activeOrder = $user
            ? Order::query()
                ->forUser($user)
                ->forEvent($event)
                ->activeReservation()
                ->first()
            : null;

        return $this->inertiaResponse->render('Events/Show', [
            'event' => EventResource::make($event),
            'activeOrder' => $activeOrder ? ['uuid' => $activeOrder->uuid, 'expires_at' => $activeOrder->expires_at->toISOString()] : null,
        ]);
    }
}
