<?php

declare(strict_types=1);

namespace App\Actions;

use App\Builders\TicketBuilder;
use App\Enums\PreservedRoleList;
use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Carbon\CarbonImmutable;

final class GetDashboardStatsAction
{
    public function pendingOrganizationsCount(User $user): int
    {
        if ($user->hasAnyRole(PreservedRoleList::OrganizationAdmin)) {
            return 0;
        }

        return Organization::query()->pending()->count();
    }

    public function cancelledOrdersCount(User $user): int
    {
        $query = Order::query()
            ->cancelled()
            ->where('cancelled_at', '>=', CarbonImmutable::now()->subDays(7));

        if ($user->hasAnyRole(PreservedRoleList::OrganizationAdmin)) {
            $query->forOrganization($user->organization_id);
        }

        return $query->count();
    }

    public function recentCancelledOrders(User $user): array
    {
        $query = Order::query()
            ->with(['user', 'event'])
            ->cancelled()
            ->where('cancelled_at', '>=', CarbonImmutable::now()->subDays(7))
            ->orderByDesc('cancelled_at')
            ->limit(5);

        if ($user->hasAnyRole(PreservedRoleList::OrganizationAdmin)) {
            $query->forOrganization($user->organization_id);
        }

        return $query->get()
            ->map(fn (Order $order) => [
                'uuid' => $order->uuid,
                'customer_name' => $order->user->name,
                'customer_email' => $order->user->email,
                'event_title' => $order->event->title,
                'total' => $order->total,
                'currency' => $order->currency,
                'cancelled_at' => $order->cancelled_at?->toISOString(),
            ])
            ->all();
    }

    public function eventsCheckInRates(User $user): array
    {
        $query = Event::query()
            ->withCount([
                'tickets',
                'tickets as checked_in_count' => fn (TicketBuilder $subQuery) => $subQuery->used(),
            ])
            ->published()
            ->where('ends_at', '>=', CarbonImmutable::now())
            ->orderBy('starts_at');

        if ($user->hasAnyRole(PreservedRoleList::OrganizationAdmin)) {
            $query->forOrganization($user->organization_id);
        }

        return $query->get()
            ->map(fn (Event $event) => [
                'uuid' => $event->uuid,
                'title' => $event->title,
                'starts_at' => $event->starts_at->toISOString(),
                'total_tickets' => $event->tickets_count,
                'checked_in_count' => $event->checked_in_count,
            ])
            ->all();
    }

    public function revenueThisMonth(User $user): array
    {
        $query = Order::query()
            ->paid()
            ->whereBetween('paid_at', [
                CarbonImmutable::now()->startOfMonth(),
                CarbonImmutable::now()->endOfMonth(),
            ]);

        if ($user->hasAnyRole(PreservedRoleList::OrganizationAdmin)) {
            $query->forOrganization($user->organization_id);
        }

        $stats = $query
            ->selectRaw('COALESCE(SUM(total), 0) as revenue, MAX(currency) as currency')
            ->first();

        return [
            'amount' => (int) ($stats?->revenue ?? 0),
            'currency' => $stats?->currency ?? 'USD',
        ];
    }

    public function ticketsSoldThisMonth(User $user): int
    {
        $query = Ticket::query()
            ->sold()
            ->whereBetween('created_at', [
                CarbonImmutable::now()->startOfMonth(),
                CarbonImmutable::now()->endOfMonth(),
            ]);

        if ($user->hasAnyRole(PreservedRoleList::OrganizationAdmin)) {
            $query->forOrganization($user->organization_id);
        }

        return $query->count();
    }

    public function draftEventsNearStartDate(User $user): array
    {
        $query = Event::query()
            ->draft()
            ->whereBetween('starts_at', [
                CarbonImmutable::now(),
                CarbonImmutable::now()->addDays(7),
            ])
            ->orderBy('starts_at');

        if ($user->hasAnyRole(PreservedRoleList::OrganizationAdmin)) {
            $query->forOrganization($user->organization_id);
        }

        return $query->get()
            ->map(fn (Event $event) => [
                'uuid' => $event->uuid,
                'title' => $event->title,
                'starts_at' => $event->starts_at->toISOString(),
            ])
            ->all();
    }

    public function recentActivity(User $user): array
    {
        if ($user->hasAnyRole(PreservedRoleList::OrganizationAdmin)) {
            return [];
        }

        return ActivityLog::query()
            ->with(['causer', 'subject'])
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(fn (ActivityLog $log) => [
                'uuid' => $log->uuid,
                'event' => ['value' => $log->event->value, 'label' => $log->event->label()],
                'causer_name' => $log->causer?->name,
                'subject_label' => match (true) {
                    $log->subject instanceof Event => $log->subject->title,
                    $log->subject instanceof Order => $log->subject->uuid,
                    $log->subject instanceof Organization => $log->subject->title,
                    default => null,
                },
                'properties' => $log->properties,
                'created_at' => $log->created_at->toISOString(),
            ])
            ->all();
    }

    public function eventsByStatus(User $user): array
    {
        $isOrgAdmin = $user->hasAnyRole(PreservedRoleList::OrganizationAdmin);

        $base = fn () => $isOrgAdmin
            ? Event::query()->forOrganization($user->organization_id)
            : Event::query();

        return [
            'draft' => $base()->draft()->count(),
            'published' => $base()->published()->count(),
            'cancelled' => $base()->cancelled()->count(),
        ];
    }
}
