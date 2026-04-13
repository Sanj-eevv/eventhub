<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\SettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

final class MyOrderController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly ResponseFactory $inertiaResponse,
        private readonly SettingsService $settingsService,
    ) {}

    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->authManager->user();

        $orders = Order::query()
            ->forUser($user)
            ->with(['event', 'tickets.ticketType'])
            ->latest()
            ->paginate(perPage: 10, page: $request->integer('page', 1));

        return $this->inertiaResponse->render('My/Orders/Index', [
            'orders' => $this->inertiaResponse->scroll(OrderResource::collection($orders)),
        ]);
    }

    public function show(Order $order): Response
    {
        $this->authorize('view', $order);

        $order->load(['tickets.ticketType', 'event']);

        $settings = $this->settingsService->get();

        return $this->inertiaResponse->render('My/Orders/Show', [
            'order' => OrderResource::make($order),
            'can_download_pdf' => OrderStatus::Paid === $order->status,
            'can_cancel' => OrderStatus::Paid === $order->status
                && $order->event->starts_at->isAfter(
                    CarbonImmutable::now()->addHours($settings->cancellationCutoffHours)
                ),
        ]);
    }
}
