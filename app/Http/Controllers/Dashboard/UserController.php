<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\UserRequest;
use App\Http\Resources\Organization\ShowResource as OrganizationShowResource;
use App\Http\Resources\Role\ShowResource as RoleShowResource;
use App\Http\Resources\User\IndexResource;
use App\Http\Resources\User\ShowResource;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $allowedSorts = ['name', 'email', 'role_name', 'organization_title', 'created_at'];
        $sortBy = collect($request->array('sort_by'))->filter(fn ($s): bool => isset($s['id'], $s['desc']) && in_array($s['id'], $allowedSorts, true));
        $search = $request->input('search', null);
        $users = DB::table('users as u')
            ->select([
                'u.uuid',
                'u.name',
                'u.email',
                'u.created_at',
                'r.name as role_name',
                'r.slug as role_slug',
                'o.title as organization_title',
                'o.uuid as organization_uuid',
            ])
            ->join('roles as r', 'r.id', '=', 'u.role_id')
            ->leftJoin('organizations as o', function (Builder $builder): void {
                $builder->whereNull('o.deleted_at')
                    ->on('o.id', '=', 'u.organization_id');
            })
            ->whereNull('u.deleted_at')
            ->when(
                $search,
                fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query->where('u.name', 'like', "%{$search}%")
                    ->orWhere('u.email', 'like', "%{$search}%")
                    ->orWhere('r.name', 'like', "%{$search}%")
                    ->orWhere('o.title', 'like', "%{$search}%")),
            )
            ->when(
                $sortBy->isNotEmpty(),
                function (Builder $query) use ($sortBy): void {
                    $sortBy->each(fn ($s) => $query->orderBy($s['id'], filter_var($s['desc'], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc'));
                },
                fn ($query) => $query->latest('u.created_at'),
            )
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return Inertia::render('Dashboard/Users/Index', [
            'users' => IndexResource::collection($users)->additional([
                'meta' => [
                    'sort' => $sortBy,
                ],
                'filters' => [
                    'search' => $search,
                ],
            ]),
            'roles' => Inertia::once(fn () => RoleShowResource::collection(Role::query()->get())),
            'organizations' => Inertia::once(fn () => OrganizationShowResource::collection(Organization::query()->get())),
        ]);
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);

        return Inertia::render('Dashboard/Users/Show', [
            'user' => ShowResource::make($user->load(['role', 'organization'])),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $this->userService->create($request->toDto());

        return redirect()->route('dashboard.users.index')->with('toastSuccess', 'User created successfully.');
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->userService->update($user, $request->toDto());

        return back()->with('toastSuccess', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        return back()->with('toastSuccess', 'User deleted.');
    }
}
