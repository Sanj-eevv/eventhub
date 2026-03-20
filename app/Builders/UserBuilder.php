<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

final class UserBuilder extends AppBuilder
{
    protected array $allowedSortColumns = ['name', 'email', 'role_name', 'organization_title', 'created_at'];

    public function forIndex(): self
    {
        return $this
            ->select([
                'users.uuid',
                'users.name',
                'users.email',
                'users.created_at',
                'r.name as role_name',
                'r.slug as role_slug',
                'o.title as organization_title',
                'o.uuid as organization_uuid',
            ])
            ->join('roles as r', 'r.id', '=', 'users.role_id')
            ->leftJoin('organizations as o', function (JoinClause $join): void {
                $join->on('o.id', '=', 'users.organization_id')
                    ->whereNull('o.deleted_at');
            });
    }

    public function search(?string $search): self
    {
        return $this->when($search, fn (self $query) => $query->where(fn (Builder $query) => $query
            ->where('users.name', 'like', "%{$search}%")
            ->orWhere('users.email', 'like', "%{$search}%")
            ->orWhere('r.name', 'like', "%{$search}%")
            ->orWhere('o.title', 'like', "%{$search}%")));
    }
}
