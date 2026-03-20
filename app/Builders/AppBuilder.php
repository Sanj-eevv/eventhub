<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

abstract class AppBuilder extends Builder
{
    protected array $allowedSortColumns = [];

    protected array $sortColumnMap = [];

    protected ?string $defaultSortColumn = null;

    final public function sortBy(?array $columns): static
    {
        $filtered = collect($columns)
            ->filter(fn ($sort): bool => isset($sort['id'], $sort['desc']) && in_array($sort['id'], $this->allowedSortColumns, true));

        if ($filtered->isEmpty()) {
            return $this->defaultSortColumn
                ? $this->latest($this->defaultSortColumn)
                : $this;
        }

        $filtered->each(fn ($sort) => $this->orderBy(
            $this->sortColumnMap[$sort['id']] ?? $sort['id'],
            filter_var($sort['desc'], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc',
        ));

        return $this;
    }
}
