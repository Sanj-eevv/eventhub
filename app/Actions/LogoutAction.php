<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

final class LogoutAction
{
    public function __construct(private AuthManager $authManager) {}

    public function execute(Request $request): void
    {
        $this->authManager->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
