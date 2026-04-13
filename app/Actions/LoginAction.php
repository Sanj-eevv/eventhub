<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Auth\AuthManager;

final readonly class LoginAction
{
    public function __construct(private AuthManager $authManager) {}

    public function __invoke(array $credentials, bool $remember): bool
    {
        return $this->authManager->attempt($credentials, $remember);
    }
}
