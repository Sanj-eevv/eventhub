<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class DeleteUserAction
{
    public function __invoke(User $user): void
    {
        $user->delete();
    }
}
