<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\EventStatus;
use App\Enums\PreservedRoleList;
use App\Models\User;
use Carbon\CarbonImmutable;
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
                'events.venue_name',
                'events.address',
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
            ->orWhere('events.venue_name', 'like', "%{$search}%")
            ->orWhere('events.address', 'like', "%{$search}%")));
    }

    public function upcoming(): self
    {
        return $this->where('events.starts_at', '>=', CarbonImmutable::now());
    }

    public function filterByStatus(?string $status): self
    {
        return $this->when($status, fn (self $query) => $query->where('events.status', $status));
    }

    public function draft(): self
    {
        return $this->where('events.status', EventStatus::Draft);
    }

    public function published(): self
    {
        return $this->where('events.status', EventStatus::Published);
    }

    public function cancelled(): self
    {
        return $this->where('events.status', EventStatus::Cancelled);
    }

    public function forOrganization(int $organizationId): self
    {
        return $this->where('events.organization_id', $organizationId);
    }

    public function forUserContext(User $user): self
    {
        return $this->when(
            $user->organization_id && ! $user->hasAnyRole(PreservedRoleList::Admin),
            fn (self $query) => $query->forOrganization($user->organization_id),
        );
    }
}
