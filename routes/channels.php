<?php

declare(strict_types=1);

use App\Enums\PreservedRoleList;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', fn (User $user, int $id): bool => $user->id === $id);

Broadcast::channel('checkin.{eventUuid}', function (User $user, string $eventUuid): bool {
    $event = Event::query()->where('uuid', $eventUuid)->first();

    if ( ! $event) {
        return false;
    }

    return $user->can('checkIn', $event);
});

Broadcast::channel('event.{eventUuid}', function (User $user, string $eventUuid): bool {
    $event = Event::query()->where('uuid', $eventUuid)->first();

    if ( ! $event) {
        return false;
    }

    return $user->can('update', $event);
});

Broadcast::channel('order.{orderUuid}', fn (User $user, string $orderUuid): bool => Order::query()
    ->where('uuid', $orderUuid)
    ->where('user_id', $user->id)
    ->exists());

Broadcast::channel('admin-approvals', fn (User $user): bool => $user->hasAnyRole(
    PreservedRoleList::Admin,
    PreservedRoleList::SuperAdmin,
));
