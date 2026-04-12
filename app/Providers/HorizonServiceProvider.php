<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\PreservedRoleList;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

final class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();
    }

    protected function gate(): void
    {
        Gate::define('viewHorizon', fn (User $user): bool => $user->hasAnyRole(PreservedRoleList::SuperAdmin));
    }
}
