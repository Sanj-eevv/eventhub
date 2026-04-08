<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\OrganizationStatus;
use Illuminate\Database\Eloquent\Builder;

final class OrganizationBuilder extends AppBuilder
{
    protected array $allowedSortColumns = ['title', 'contact_email', 'status', 'created_at'];

    public function approved(): self
    {
        return $this->where('status', OrganizationStatus::Approved);
    }

    public function pending(): self
    {
        return $this->where('status', OrganizationStatus::Pending);
    }

    public function forIndex(): self
    {
        return $this->select(['uuid', 'title', 'contact_email', 'status', 'created_at']);
    }

    public function search(?string $search): self
    {
        return $this->when($search, fn (self $query) => $query->where(fn (Builder $query) => $query
            ->where('title', 'like', "%{$search}%")
            ->orWhere('contact_email', 'like', "%{$search}%")));
    }

    public function filterByStatus(?string $status): self
    {
        return $this->when($status, fn (self $query) => $query->where('status', $status));
    }
}
