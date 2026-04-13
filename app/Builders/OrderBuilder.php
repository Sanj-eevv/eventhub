<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\OrderStatus;
use App\Enums\PreservedRoleList;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/** @extends AppBuilder<Order> */
final class OrderBuilder extends AppBuilder
{
    protected array $allowedSortColumns = ['total', 'paid_at', 'created_at'];

    protected ?string $defaultSortColumn = 'created_at';

    public function forIndex(): static
    {
        return $this->with(['event', 'user']);
    }

    public function search(?string $search): static
    {
        if (blank($search)) {
            return $this;
        }

        return $this->where(function (Builder $query) use ($search): void {
            $query->whereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', sprintf('%%%s%%', $search))->orWhere('email', 'like', sprintf('%%%s%%', $search)))
                ->orWhereHas('event', fn (Builder $eventQuery) => $eventQuery->where('title', 'like', sprintf('%%%s%%', $search)));
        });
    }

    public function forUser(User $user): self
    {
        return $this->where('user_id', $user->id);
    }

    public function forEvent(Event $event): self
    {
        return $this->where('event_id', $event->id);
    }

    public function activeReservation(): self
    {
        return $this
            ->where('status', OrderStatus::Reserved)
            ->where('expires_at', '>', now());
    }

    public function paid(): self
    {
        return $this->where('status', OrderStatus::Paid);
    }

    public function cancelled(): self
    {
        return $this->where('status', OrderStatus::Cancelled);
    }

    public function forOrganization(?int $organizationId): self
    {
        return $this->whereHas('event', fn (Builder $query) => $query->where('organization_id', $organizationId));
    }

    public function forUserContext(User $user): self
    {
        return $this->when(
            $user->organization_id && ! $user->hasAnyRole(PreservedRoleList::Admin),
            fn (self $query): OrderBuilder => $query->forOrganization($user->organization_id),
        );
    }
}
