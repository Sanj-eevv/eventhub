<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\IndexResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

final class OrderController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Order::class);

        $search = $request->string('search')->toString() ?: null;
        $sortBy = $request->array('sort_by');

        /** @var User $user */
        $user = $this->authManager->user();

        $orders = Order::query()
            ->forIndex()
            ->forUserContext($user)
            ->search($search)
            ->sortBy($sortBy)
            ->paginate(perPage: $request->integer('per_page', 10), page: $request->integer('page', 1));

        return $this->inertiaResponse->render('Dashboard/Orders/Index', [
            'orders' => IndexResource::collection($orders)->additional([
                'meta' => ['sort' => $sortBy],
                'filters' => ['search' => $search],
            ]),
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
