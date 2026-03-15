<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Enums\OrganizationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Organizations\OrganizationRequest;
use App\Http\Resources\Organization\IndexResource;
use App\Http\Resources\Organization\ShowResource;
use App\Models\Organization;
use App\Services\OrganizationService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class OrganizationController extends Controller
{
    public function __construct(
        private readonly OrganizationService $organizationService,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Organization::class);

        $allowedSorts = ['title', 'contact_email', 'status', 'created_at'];
        $sortBy = collect($request->array('sort_by'))->filter(fn ($s): bool => isset($s['id'], $s['desc']) && in_array($s['id'], $allowedSorts, true));
        $search = $request->input('search', null);
        $status = $request->input('status', null);
        $organizations = DB::table('organizations')
            ->select('uuid', 'title', 'contact_email', 'status', 'created_at')
            ->whereNull('deleted_at')
            ->when(
                $search,
                fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query->where('title', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")),
            )
            ->when(
                $status,
                fn (Builder $query): Builder => $query->where('status', $status),
            )
            ->when(
                $sortBy->isNotEmpty(),
                function (Builder $query) use ($sortBy): void {
                    $sortBy->each(fn ($s) => $query->orderBy($s['id'], filter_var($s['desc'], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc'));
                },
                fn ($query) => $query->latest(),
            )
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return Inertia::render(
            'Dashboard/Organizations/Index',
            [
                'organizations' => IndexResource::collection($organizations)->additional(
                    [
                        'meta' => [
                            'sort' => $sortBy,
                        ],
                        'filters' => [
                            'search' => $search,
                            'status' => $status,
                        ],
                    ],
                ),
            ],
        );
    }

    public function show(Organization $organization): Response
    {
        $this->authorize('view', $organization);

        return Inertia::render('Dashboard/Organizations/Show', [
            'organization' => ShowResource::make($organization),
        ]);
    }

    public function store(OrganizationRequest $request): RedirectResponse
    {
        $this->organizationService->create($request->toDto());

        return redirect()->route('dashboard.organizations.index')->with('toastSuccess', 'Organization created successfully.');
    }

    public function update(OrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $this->organizationService->update($organization, $request->toDto());

        return back()->with('toastSuccess', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('delete', Organization::class);

        $this->organizationService->delete($organization);

        return back()->with('toastSuccess', 'Organization deleted successfully.');
    }

    public function confirmStatus(Request $request, Organization $organization, string $action): RedirectResponse
    {
        $this->authorize('approve', $organization);
        if ( ! in_array($action, ['approve', 'reject'], true)) {
            abort(400, 'Invalid action');
        }

        $status = 'approve' === $action ? OrganizationStatus::Approved : OrganizationStatus::Rejected;
        $this->organizationService->confirmStatus($organization, $status, ! $request->boolean('send_notification', true));

        return back()->with('toastSuccess', 'Organization deleted.');
    }
}
