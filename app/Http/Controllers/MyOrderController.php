<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

final class MyOrderController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->forUser($request->user())
            ->with(['event', 'tickets.ticketType'])
            ->latest()
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return $this->inertiaResponse->render('My/Orders/Index', [
            'orders' => OrderResource::collection($orders),
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        $this->authorize('view', $order);

        $order->load(['tickets.ticketType', 'event']);

        return $this->inertiaResponse->render('My/Orders/Show', [
            'order' => OrderResource::make($order),
        ]);
    }
}
