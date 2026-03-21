<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

final class DashboardOrderController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::query()
            ->with(['event', 'tickets.ticketType', 'user'])
            ->latest()
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return $this->inertiaResponse->render('Dashboard/Orders/Index', [
            'orders' => OrderResource::collection($orders),
        ]);
    }

    public function show(Order $order): Response
    {
        $this->authorize('view', $order);

        $order->load(['tickets.ticketType', 'event', 'user']);

        return $this->inertiaResponse->render('Dashboard/Orders/Show', [
            'order' => OrderResource::make($order),
        ]);
    }
}
