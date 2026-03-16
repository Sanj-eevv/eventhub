<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Auth\AuthManager;

final class LoginAction
{
    public function __construct(private readonly AuthManager $authManager) {}

    public function execute(array $credentials, bool $remember): bool
    {
        return $this->authManager->attempt($credentials, $remember);
    }
}
