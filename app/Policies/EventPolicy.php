<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\CheckInPermissions;
use App\Enums\EventPermissions;
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

    public function view(User $user): bool
    {
        return $user->hasPermission(EventPermissions::View);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(EventPermissions::Create);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(EventPermissions::Update);
    }

    public function publish(User $user): bool
    {
        return $user->hasPermission(EventPermissions::Publish);
    }

    public function unpublish(User $user): bool
    {
        return $user->hasPermission(EventPermissions::Publish);
    }

    public function cancel(User $user): bool
    {
        return $user->hasPermission(EventPermissions::Cancel);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(EventPermissions::Delete);
    }

    public function checkIn(User $user, Event $event): bool
    {
        if ( ! $user->hasPermission(CheckInPermissions::Manage)) {
            return false;
        }

        if ($user->hasAnyRole(PreservedRoleList::Admin)) {
            return true;
        }

        return $this->withinOrganization($user, $event);
    }
}
