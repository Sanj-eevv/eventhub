<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\EventPermissions;
use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\User;

final class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            EventPermissions::ALLOW_CREATE,
            EventPermissions::ALLOW_UPDATE,
            EventPermissions::ALLOW_DELETE,
        ]);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(EventPermissions::ALLOW_CREATE);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(EventPermissions::ALLOW_UPDATE);
    }

    public function publish(User $user, Event $event): bool
    {
        return EventStatus::Draft === $event->status;
    }

    public function cancel(User $user, Event $event): bool
    {
        return EventStatus::Published === $event->status;
    }

    public function unpublish(User $user, Event $event): bool
    {
        return EventStatus::Published === $event->status;
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(EventPermissions::ALLOW_DELETE);
    }
}
