<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetDashboardStatsAction;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly GetDashboardStatsAction $getDashboardStatsAction,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function __invoke(): Response
    {
        $this->authorize('access-dashboard');

        /** @var User $user */
        $user = $this->authManager->user();

        return $this->inertiaResponse->render('Dashboard/Index', [
            'pendingOrganizationsCount' => fn (): int => $this->getDashboardStatsAction->pendingOrganizationsCount($user),
            'cancelledOrdersCount' => fn (): int => $this->getDashboardStatsAction->cancelledOrdersCount($user),
            'revenueThisMonth' => fn (): array => $this->getDashboardStatsAction->revenueThisMonth($user),
            'ticketsSoldThisMonth' => fn (): int => $this->getDashboardStatsAction->ticketsSoldThisMonth($user),
            'eventsByStatus' => fn (): array => $this->getDashboardStatsAction->eventsByStatus($user),
            'draftEventsNearStartDate' => fn (): array => $this->getDashboardStatsAction->draftEventsNearStartDate($user),
            'recentCancelledOrders' => Inertia::defer(fn (): array => $this->getDashboardStatsAction->recentCancelledOrders($user)),
            'eventsCheckInRates' => Inertia::defer(fn (): array => $this->getDashboardStatsAction->eventsCheckInRates($user)),
            'recentActivity' => Inertia::defer(fn (): array => $this->getDashboardStatsAction->recentActivity($user)),
        ]);
    }
}
