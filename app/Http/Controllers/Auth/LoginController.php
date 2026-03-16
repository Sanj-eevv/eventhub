<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\LoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Inertia\Response;
use Inertia\ResponseFactory;

final class LoginController extends Controller
{
    public function __construct(
        private readonly LoginAction $loginAction,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function create(): Response
    {
        return $this->inertiaResponse->render('Auth/Login');
    }

    public function store(LoginRequest $loginRequest): RedirectResponse
    {
        if ($this->loginAction->execute($loginRequest->validated(), $loginRequest->boolean('remember'))) {
            $loginRequest->session()->regenerate();

            return $this->redirector->intended($this->urlGenerator->route('home', absolute: false));
        }

        return $this->redirector->back()->withErrors(['email' => 'These credentials do not match our records.'])->withInput($loginRequest->except('password'));
    }
}
