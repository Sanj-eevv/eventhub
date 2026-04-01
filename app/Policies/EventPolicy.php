<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\CheckInPermissions;
use App\Enums\EventPermissions;
use App\Enums\PreservedRoleList;
use App\Models\Event;
use App\Models\User;

final class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            EventPermissions::AllowCreate,
            EventPermissions::AllowUpdate,
            EventPermissions::AllowDelete,
        ]);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(EventPermissions::AllowCreate);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(EventPermissions::AllowUpdate);
    }

    public function publish(User $user, Event $event): bool
    {
        return $user->hasPermission(EventPermissions::AllowUpdate);
    }

    public function cancel(User $user, Event $event): bool
    {
        return $user->hasPermission(EventPermissions::AllowUpdate);
    }

    public function unpublish(User $user, Event $event): bool
    {
        return $user->hasPermission(EventPermissions::AllowUpdate);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(EventPermissions::AllowDelete);
    }

    public function checkIn(User $user, Event $event): bool
    {
        if ( ! $user->hasPermission(CheckInPermissions::AllowManage)) {
            return false;
        }

        if ($user->hasAnyRole([PreservedRoleList::SuperAdmin->value, PreservedRoleList::Admin->value])) {
            return true;
        }

        return $user->organization_id === $event->organization_id;
    }
}
