<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

final class RoleBuilder extends AppBuilder
{
    protected array $allowedSortColumns = ['name', 'slug', 'preserved', 'created_at'];

    public function forIndex(): self
    {
        return $this->select(['id', 'name', 'slug', 'preserved', 'created_at']);
    }

    public function search(?string $search): self
    {
        return $this->when($search, fn (self $query) => $query->where(fn (Builder $query) => $query
            ->where('name', 'like', "%{$search}%")
            ->orWhere('slug', 'like', "%{$search}%")));
    }
}
