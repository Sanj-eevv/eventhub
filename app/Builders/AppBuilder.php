<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends Builder<TModel>
 */
abstract class AppBuilder extends Builder
{
    protected array $allowedSortColumns = [];

    protected array $sortColumnMap = [];

    protected ?string $defaultSortColumn = null;

    final public function sortBy(?array $columns): static
    {
        $filtered = collect($columns)
            ->filter(fn ($sortItem): bool => isset($sortItem['id'], $sortItem['desc']) && in_array($sortItem['id'], $this->allowedSortColumns, true));

        if ($filtered->isEmpty()) {
            return $this->defaultSortColumn
                ? $this->latest($this->defaultSortColumn)
                : $this;
        }

        $filtered->each(fn ($sortItem) => $this->orderBy(
            $this->sortColumnMap[$sortItem['id']] ?? $sortItem['id'],
            filter_var($sortItem['desc'], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc',
        ));

        return $this;
    }
}
