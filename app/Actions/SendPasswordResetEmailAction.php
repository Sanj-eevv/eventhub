<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Contracts\Auth\PasswordBroker;

final readonly class SendPasswordResetEmailAction
{
    public function __construct(private PasswordBroker $passwordBroker) {}

    public function execute(string $email): void
    {
        $this->passwordBroker->sendResetLink(['email' => $email]);
    }
}
