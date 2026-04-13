<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Auth\AuthManager;

final readonly class LogoutAction
{
    public function __construct(private AuthManager $authManager) {}

    public function __invoke(): void
    {
        $this->authManager->logout();
    }
}
