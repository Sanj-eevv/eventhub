<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Events\Dispatcher;

final class VerifyEmailAction
{
    public function __construct(private readonly Dispatcher $dispatcher) {}

    public function execute(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->markEmailAsVerified();

        $this->dispatcher->dispatch(new Verified($user));
    }
}
