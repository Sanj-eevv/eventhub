<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrderPermissions;
use App\Models\Order;
use App\Models\User;

final class OrderPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(OrderPermissions::View);
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->hasPermission(OrderPermissions::View)) {
            return true;
        }

        return $order->user_id === $user->id;
    }

    public function cancel(User $user, ?Order $order = null): bool
    {
        if ($user->hasPermission(OrderPermissions::Cancel)) {
            return true;
        }

        return $order instanceof Order && $order->user_id === $user->id;
    }
}
