<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CreateUserAction;
use App\Actions\DeleteUserAction;
use App\Actions\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UserRequest;
use App\Http\Resources\Organization\PickerResource as OrganizationPickerResource;
use App\Http\Resources\Role\PickerResource as RolePickerResource;
use App\Http\Resources\User\IndexResource;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class UserController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $search = $request->input('search');
        $sortBy = $request->array('sort_by');

        /** @var User $authUser */
        $authUser = $this->authManager->user();

        $users = User::query()
            ->forIndex()
            ->forUserContext($authUser)
            ->search($search)
            ->sortBy($sortBy)
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return $this->inertiaResponse->render('Dashboard/Users/Index', [
            'users' => IndexResource::collection($users)->additional([
                'meta' => [
                    'sort' => $sortBy,
                ],
                'filters' => [
                    'search' => $search,
                ],
            ]),
            'roles' => $this->inertiaResponse->once(fn () => RolePickerResource::collection(Role::query()->get())),
            'organizations' => $this->inertiaResponse->once(fn () => OrganizationPickerResource::collection(Organization::query()->get())),
        ]);
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);

        return $this->inertiaResponse->render('Dashboard/Users/Show', [
            'user' => UserResource::make($user->load(['role', 'organization'])),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $this->createUserAction->execute($request->toDto());

        return $this->redirector->route('dashboard.users.index')->with('toast_success', 'User created successfully.');
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->updateUserAction->execute($user, $request->toDto());

        return $this->redirector->back()->with('toast_success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->deleteUserAction->execute($user);

        return $this->redirector->back()->with('toast_success', 'User deleted.');
    }
}
