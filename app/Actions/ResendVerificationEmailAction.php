<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class ResendVerificationEmailAction
{
    public function execute(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }
}
