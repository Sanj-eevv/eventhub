<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CreateEventAction;
use App\Actions\DeleteEventAction;
use App\Actions\UpdateEventAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\EventRequest;
use App\Http\Resources\Event\EditResource;
use App\Http\Resources\Event\IndexResource;
use App\Http\Resources\Event\ShowResource;
use App\Http\Resources\Organization\PickerResource as OrganizationPickerResource;
use App\Models\Event;
use App\Models\Organization;
use DateTimeZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class EventController extends Controller
{
    public function __construct(
        private readonly CreateEventAction $createEventAction,
        private readonly UpdateEventAction $updateEventAction,
        private readonly DeleteEventAction $deleteEventAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Event::class);

        $search = $request->input('search');
        $status = $request->input('status');
        $sortBy = $request->array('sort_by');

        $events = Event::query()
            ->forIndex()
            ->search($search)
            ->filterByStatus($status)
            ->sortBy($sortBy)
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return $this->inertiaResponse->render('Dashboard/Events/Index', [
            'events' => IndexResource::collection($events)->additional([
                'meta' => ['sort' => $sortBy],
                'filters' => ['search' => $search, 'status' => $status],
            ]),
        ]);
    }

    public function show(Event $event): Response
    {
        $this->authorize('view', $event);

        return $this->inertiaResponse->render('Dashboard/Events/Show', [
            'event' => ShowResource::make($event->load(['organization', 'user'])),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Event::class);

        return $this->inertiaResponse->render('Dashboard/Events/Create', [
            'organizations' => OrganizationPickerResource::collection(Organization::query()->approved()->get()),
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $event = $this->createEventAction->execute($request->toDto());

        return $this->redirector->route('dashboard.events.edit', ['event' => $event, 'focus' => 'media'])->with('toast_success', 'Event created successfully.');
    }

    public function edit(Event $event): Response
    {
        $this->authorize('update', $event);

        return $this->inertiaResponse->render('Dashboard/Events/Edit', [
            'event' => new EditResource($event->load(['organization', 'user', 'ticketTypes', 'media'])),
            'organizations' => OrganizationPickerResource::collection(Organization::query()->approved()->get()),
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $this->updateEventAction->execute($event, $request->toDto());

        return $this->redirector->back()->with('toast_success', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $this->deleteEventAction->execute($event);

        return $this->redirector->back()->with('toast_success', 'Event deleted.');
    }
}
