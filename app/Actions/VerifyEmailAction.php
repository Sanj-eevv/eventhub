<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class VerifyEmailAction
{
    public function execute(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }
        $user->markEmailAsVerified();
    }
}
