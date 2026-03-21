<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\CheckInPermissions;
use App\Models\Ticket;
use App\Models\User;

final class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $ticket->user_id === $user->id || $user->hasPermission(CheckInPermissions::AllowManage);
    }
}
