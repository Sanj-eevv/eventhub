<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\AuthManager;

final class LoginAction
{
    public function __construct(private AuthManager $authManager) {}

    public function execute(LoginRequest $request): bool
    {
        $loggedIn = $this->authManager->attempt($request->validated(), $request->boolean('remember'));

        $loggedIn && $request->session()->regenerate();

        return $loggedIn;
    }
}
