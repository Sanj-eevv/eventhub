<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

final class RoleBuilder extends Builder
{
    private array $allowedSortableColumn = ['name', 'slug', 'preserved', 'created_at'];

    public function search(?string $search): self
    {
        return $this->when($search, fn (Builder $builder) => $builder->where(fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%")
            ->orWhere('slug', 'like', "%{$search}%")));
    }

    public function sortBy(?array $columns): self
    {
        $sortBy = collect($columns)->filter(fn ($s): bool => isset($s['id'], $s['desc']) && in_array($s['id'], $this->allowedSortableColumn, true));

        return $this->when(
            $sortBy->isNotEmpty(),
            fn (Builder $query) => $sortBy->each(fn (array $s) => $query->orderBy($s['id'], filter_var($s['desc'], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc'))
        );
    }
}
