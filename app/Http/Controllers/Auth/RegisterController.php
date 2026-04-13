<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\CreateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Inertia\Response;
use Inertia\ResponseFactory;

final class RegisterController extends Controller
{
    public function __construct(
        private readonly CreateUserAction $createUserAction,
        private readonly AuthManager $authManager,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
        private readonly ResponseFactory $inertiaResponse,
        private readonly Dispatcher $dispatcher,
    ) {}

    public function create(): Response
    {
        return $this->inertiaResponse->render('Auth/Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = ($this->createUserAction)($request->toDto());

        $this->dispatcher->dispatch(new Registered($user));

        $this->authManager->login($user);

        return $this->redirector->intended($this->urlGenerator->route('home', absolute: false));
    }
}
