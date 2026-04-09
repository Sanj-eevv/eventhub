<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetDashboardStatsAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardStatsAction $getDashboardStatsAction,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function __invoke(Request $request): Response
    {
        $this->authorize('access-dashboard');

        $user = $request->user();

        return $this->inertiaResponse->render('Dashboard/Index', [
            'pendingOrganizationsCount' => fn () => $this->getDashboardStatsAction->pendingOrganizationsCount($user),
            'cancelledOrdersCount' => fn () => $this->getDashboardStatsAction->cancelledOrdersCount($user),
            'revenueThisMonth' => fn () => $this->getDashboardStatsAction->revenueThisMonth($user),
            'ticketsSoldThisMonth' => fn () => $this->getDashboardStatsAction->ticketsSoldThisMonth($user),
            'eventsByStatus' => fn () => $this->getDashboardStatsAction->eventsByStatus($user),
            'draftEventsNearStartDate' => fn () => $this->getDashboardStatsAction->draftEventsNearStartDate($user),
            'recentCancelledOrders' => Inertia::defer(fn () => $this->getDashboardStatsAction->recentCancelledOrders($user)),
            'eventsCheckInRates' => Inertia::defer(fn () => $this->getDashboardStatsAction->eventsCheckInRates($user)),
            'recentActivity' => Inertia::defer(fn () => $this->getDashboardStatsAction->recentActivity($user)),
        ]);
    }
}
