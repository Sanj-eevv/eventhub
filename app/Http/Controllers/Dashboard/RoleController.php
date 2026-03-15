<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Roles\RoleRequest;
use App\Http\Resources\Role\IndexResource;
use App\Http\Resources\Role\ShowResource;
use App\Models\Role;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly PermissionService $permissionService,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Role::class);

        $search = $request->input('search');
        $sortBy = $request->array('sortBy');
        $roles = Role::query()
            ->select(['id', 'name', 'slug', 'preserved', 'created_at'])
            ->search($search)
            ->sortBy($sortBy)
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return Inertia::render('Dashboard/Roles/Index', [
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

        return Inertia::render('Dashboard/Roles/Create', [
            'groupedPermissions' => $this->permissionService->getGrouppedPermissions(),
        ]);
    }

    public function show(Role $role): Response
    {
        $this->authorize('view', $role);

        return Inertia::render('Dashboard/Roles/Show', [
            'role' => ShowResource::make($role),
            'groupedPermissions' => $this->permissionService->getGrouppedPermissions($role) ?: null,
        ]);
    }

    public function edit(Role $role): Response
    {
        $this->authorize('update', $role);
        $role->load('permissions');

        return Inertia::render('Dashboard/Roles/Edit', [
            'role' => ShowResource::make($role),
            'groupedPermissions' => $this->permissionService->getGrouppedPermissions(),
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $this->roleService->create($request->toDto());

        return redirect()->route('dashboard.roles.index')->with('toastSuccess', 'Role created successfully.');
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $this->roleService->update($role, $request->toDto());

        return back()->with('toastSuccess', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->roleService->delete($role);

        return back()->with('toastSuccess', 'Role deleted.');
    }
}
