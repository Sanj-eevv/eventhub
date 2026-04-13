<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\RegisterOrganizationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OrganizationRegisterRequest;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Inertia\Response;
use Inertia\ResponseFactory;

final class OrganizationRegisterController extends Controller
{
    public function __construct(
        private readonly RegisterOrganizationAction $registerOrganizationAction,
        private readonly AuthManager $authManager,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
        private readonly ResponseFactory $inertiaResponse,
        private readonly Dispatcher $dispatcher,
    ) {}

    public function create(): Response
    {
        return $this->inertiaResponse->render('Auth/Organization/RegisterForm/Register');
    }

    public function store(OrganizationRegisterRequest $request): RedirectResponse
    {
        $user = ($this->registerOrganizationAction)($request->toOrganizationDto(), $request->toUserDto());

        $this->dispatcher->dispatch(new Registered($user));

        $this->authManager->login($user);

        return $this->redirector->intended($this->urlGenerator->route('dashboard.index', absolute: false));
    }
}
