<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Contracts\Auth\PasswordBroker;

final class SendPasswordResetEmailAction
{
    public function __construct(private readonly PasswordBroker $passwordBroker) {}

    public function execute(string $email): void
    {
        $this->passwordBroker->sendResetLink(['email' => $email]);
    }
}
