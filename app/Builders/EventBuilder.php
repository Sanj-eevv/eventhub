<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

final class EventBuilder extends AppBuilder
{
    protected array $allowedSortColumns = ['title', 'organization', 'status', 'starts_at', 'ends_at', 'created_at'];

    protected array $sortColumnMap = ['organization' => 'organization_title'];

    protected ?string $defaultSortColumn = 'events.created_at';

    public function forIndex(): self
    {
        return $this
            ->select([
                'events.uuid',
                'events.title',
                'events.status',
                'events.starts_at',
                'events.ends_at',
                'events.created_at',
                'o.uuid as organization_uuid',
                'o.title as organization_title',
            ])
            ->leftJoin('organizations as o', function (JoinClause $join): void {
                $join->on('o.id', '=', 'events.organization_id')
                    ->whereNull('o.deleted_at');
            });
    }

    public function search(?string $search): self
    {
        return $this->when($search, fn (self $query) => $query->where(fn (Builder $query) => $query
            ->where('events.title', 'like', "%{$search}%")
            ->orWhere('events.description', 'like', "%{$search}%")
            ->orWhere('o.title', 'like', "%{$search}%")));
    }

    public function filterByStatus(?string $status): self
    {
        return $this->when($status, fn (self $query) => $query->where('events.status', $status));
    }
}
