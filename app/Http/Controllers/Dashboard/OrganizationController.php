<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CreateOrganizationAction;
use App\Actions\DeleteOrganizationAction;
use App\Actions\UpdateOrganizationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\OrganizationRequest;
use App\Http\Resources\Organization\IndexResource;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class OrganizationController extends Controller
{
    public function __construct(
        private readonly CreateOrganizationAction $createOrganizationAction,
        private readonly UpdateOrganizationAction $updateOrganizationAction,
        private readonly DeleteOrganizationAction $deleteOrganizationAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Organization::class);

        $search = $request->input('search');
        $status = $request->input('status');
        $sortBy = $request->array('sort_by');

        $organizations = Organization::query()
            ->forIndex()
            ->search($search)
            ->filterByStatus($status)
            ->sortBy($sortBy)
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return $this->inertiaResponse->render('Dashboard/Organizations/Index', [
            'organizations' => IndexResource::collection($organizations)->additional([
                'meta' => [
                    'sort' => $sortBy,
                ],
                'filters' => [
                    'search' => $search,
                    'status' => $status,
                ],
            ]),
        ]);
    }

    public function show(Organization $organization): Response
    {
        $this->authorize('view', $organization);

        return $this->inertiaResponse->render('Dashboard/Organizations/Show', [
            'organization' => OrganizationResource::make($organization),
        ]);
    }

    public function store(OrganizationRequest $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        ($this->createOrganizationAction)($request->toDto());

        return $this->redirector->route('dashboard.organizations.index')->with('toast_success', 'Organization created successfully.');
    }

    public function update(OrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $this->authorize('update', $organization);

        ($this->updateOrganizationAction)($organization, $request->toDto());

        return $this->redirector->back()->with('toast_success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('delete', $organization);

        ($this->deleteOrganizationAction)($organization);

        return $this->redirector->back()->with('toast_success', 'Organization deleted successfully.');
    }
}
