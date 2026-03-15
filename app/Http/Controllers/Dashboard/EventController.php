<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Enums\OrganizationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Events\EventRequest;
use App\Http\Resources\Event\IndexResource;
use App\Http\Resources\Event\ShowResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Organization;
use App\Services\EventService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Event::class);

        $allowedSorts = ['title', 'organization', 'status', 'starts_at', 'ends_at', 'created_at'];
        $sortBy = collect($request->array('sort_by'))->filter(fn ($s): bool => isset($s['id'], $s['desc']) && in_array($s['id'], $allowedSorts, true));
        $search = $request->input('search', null);
        $statusFilter = $request->input('status', null);
        $sortColumnMap = ['organization' => 'organization_title'];

        $events = DB::table('events as e')
            ->select([
                'e.uuid',
                'e.title',
                'e.status',
                'e.starts_at',
                'e.ends_at',
                'e.created_at',
                'o.uuid as organization_uuid',
                'o.title as organization_title',
            ])
            ->leftJoin('organizations as o', function (Builder $builder): void {
                $builder->whereNull('o.deleted_at')
                    ->on('o.id', '=', 'e.organization_id');
            })
            ->whereNull('e.deleted_at')
            ->when(
                $search,
                fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query->where('e.title', 'like', "%{$search}%")
                    ->orWhere('e.description', 'like', "%{$search}%")
                    ->orWhere('o.title', 'like', "%{$search}%")),
            )
            ->when(
                $statusFilter,
                fn (Builder $query): Builder => $query->where('e.status', $statusFilter),
            )
            ->when(
                $sortBy->isNotEmpty(),
                function (Builder $query) use ($sortBy, $sortColumnMap): void {
                    $sortBy->each(fn ($s) => $query->orderBy($sortColumnMap[$s['id']] ?? $s['id'], filter_var($s['desc'], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc'));
                },
                fn ($query) => $query->latest('e.created_at'),
            )
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return Inertia::render('Dashboard/Events/Index', [
            'events' => IndexResource::collection($events)->additional([
                'meta' => ['sort' => $sortBy],
                'filters' => ['search' => $search, 'status' => $statusFilter],
            ]),
        ]);
    }

    public function show(Event $event): Response
    {
        $this->authorize('view', $event);

        return Inertia::render('Dashboard/Events/Show', [
            'event' => ShowResource::make($event->load(['organization', 'user'])),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Event::class);

        return Inertia::render('Dashboard/Events/Create', [
            'organizations' => Organization::query()
                ->where('status', OrganizationStatus::Approved)
                ->get(['id', 'title', 'uuid']),
        ]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $this->eventService->create($request->toDto());

        return redirect()->route('dashboard.events.index')->with('toastSuccess', 'Event created successfully.');
    }

    public function edit(Event $event): Response
    {
        $this->authorize('update', $event);

        return Inertia::render('Dashboard/Events/Edit', [
            'event' => new EventResource($event->load(['organization', 'user'])),
            'organizations' => Organization::query()
                ->where('status', OrganizationStatus::Approved)
                ->get(['id', 'title', 'uuid']),
        ]);
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $this->eventService->update($event, $request->toDto());

        return back()->with('toastSuccess', 'Event updated successfully.');
    }

    public function publish(Event $event): RedirectResponse
    {
        $this->authorize('publish', $event);

        $this->eventService->publish($event);

        return back()->with('toastSuccess', 'Event published successfully.');
    }

    public function cancel(Event $event): RedirectResponse
    {
        $this->authorize('cancel', $event);

        $this->eventService->cancel($event);

        return back()->with('toastSuccess', 'Event cancelled.');
    }

    public function unpublish(Event $event): RedirectResponse
    {
        $this->authorize('unpublish', $event);

        $this->eventService->unpublish($event);

        return back()->with('toastSuccess', 'Event unpublished.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $this->eventService->delete($event);

        return back()->with('toastSuccess', 'Event deleted.');
    }
}
