<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CreateRoleAction;
use App\Actions\DeleteRoleAction;
use App\Actions\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RoleRequest;
use App\Http\Resources\Role\IndexResource;
use App\Http\Resources\Role\ShowResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class RoleController extends Controller
{
    public function __construct(
        private readonly CreateRoleAction $createRoleAction,
        private readonly UpdateRoleAction $updateRoleAction,
        private readonly DeleteRoleAction $deleteRoleAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Role::class);

        $search = $request->input('search');
        $sortBy = $request->array('sort_by');
        $roles = Role::query()
            ->forIndex()
            ->search($search)
            ->sortBy($sortBy)
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return $this->inertiaResponse->render('Dashboard/Roles/Index', [
            'roles' => IndexResource::collection($roles)->additional([
                'meta' => [
                    'sort' => $sortBy,
                ],
                'filters' => [
                    'search' => $search,
                ],
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Role::class);

        return $this->inertiaResponse->render('Dashboard/Roles/Create', [
            'groupedPermissions' => Permission::grouped(),
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $this->createRoleAction->execute($request->toDto());

        return $this->redirector->route('dashboard.roles.index')->with('toastSuccess', 'Role created successfully.');
    }

    public function show(Role $role): Response
    {
        $this->authorize('view', $role);

        $role->loadMissing('permissions');

        return $this->inertiaResponse->render('Dashboard/Roles/Show', [
            'role' => ShowResource::make($role),
            'groupedPermissions' => Permission::grouped($role) ?: null,
        ]);
    }

    public function edit(Role $role): Response
    {
        $this->authorize('update', $role);

        $role->loadMissing('permissions');

        return $this->inertiaResponse->render('Dashboard/Roles/Edit', [
            'role' => ShowResource::make($role),
            'groupedPermissions' => Permission::grouped(),
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $this->updateRoleAction->execute($role, $request->toDto());

        return $this->redirector->back()->with('toastSuccess', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->deleteRoleAction->execute($role);

        return $this->redirector->back()->with('toastSuccess', 'Role deleted.');
    }
}
