<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\EventPermissions;
use App\Enums\EventStatus;
use App\Enums\PreservedRoleList;
use App\Models\Event;
use App\Models\User;
use App\Traits\ScopedToOrganization;

final class EventPolicy extends BasePolicy
{
    use ScopedToOrganization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission(EventPermissions::View);
    }

    public function view(User $user, Event $event): bool
    {
        if ( ! $user->hasPermission(EventPermissions::View)) {
            return false;
        }

        if ($user->hasAnyRole(PreservedRoleList::Admin)) {
            return true;
        }

        return $this->withinOrganization($user, $event);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(EventPermissions::Create);
    }

    public function update(User $user, ?Event $event = null): bool
    {
        if ( ! $user->hasPermission(EventPermissions::Update)) {
            return false;
        }

        if (null === $event || $user->hasAnyRole(PreservedRoleList::Admin)) {
            return true;
        }

        return $this->withinOrganization($user, $event);
    }

    public function publish(User $user, ?Event $event = null): bool
    {
        if ( ! $user->hasPermission(EventPermissions::Publish)) {
            return false;
        }

        if (null === $event || $user->hasAnyRole(PreservedRoleList::Admin)) {
            return true;
        }

        return $this->withinOrganization($user, $event);
    }

    public function unpublish(User $user, ?Event $event = null): bool
    {
        if ( ! $user->hasPermission(EventPermissions::Unpublish)) {
            return false;
        }

        if (null === $event || $user->hasAnyRole(PreservedRoleList::Admin)) {
            return true;
        }

        return $this->withinOrganization($user, $event);
    }

    public function cancel(User $user, ?Event $event = null): bool
    {
        if ( ! $user->hasPermission(EventPermissions::Cancel)) {
            return false;
        }

        if (null === $event || $user->hasAnyRole(PreservedRoleList::Admin)) {
            return true;
        }

        return $this->withinOrganization($user, $event);
    }

    public function delete(User $user, ?Event $event = null): bool
    {
        if ( ! $user->hasPermission(EventPermissions::Delete)) {
            return false;
        }

        if (null === $event || $user->hasAnyRole(PreservedRoleList::Admin)) {
            return true;
        }

        return $this->withinOrganization($user, $event);
    }

    public function reserve(User $user, Event $event): bool
    {
        return EventStatus::Published === $event->status;
    }

    public function checkIn(User $user, ?Event $event = null): bool
    {
        if ( ! $user->hasPermission(EventPermissions::CheckIn)) {
            return false;
        }

        if (null === $event || $user->hasAnyRole(PreservedRoleList::Admin)) {
            return true;
        }

        return $this->withinOrganization($user, $event);
    }
}
