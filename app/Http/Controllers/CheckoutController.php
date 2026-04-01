<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class CheckoutController extends Controller
{
    public function __construct(
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function show(Order $order): Response|RedirectResponse
    {
        $this->authorize('view', $order);

        if (OrderStatus::Reserved !== $order->status) {
            return $this->redirector->route('events.show', ['event' => $order->event->slug])->with('toast_info', 'This order is no longer available for checkout.');
        }

        $order->load(['tickets.ticketType', 'event']);

        return $this->inertiaResponse->render('Checkout/Show', [
            'order' => OrderResource::make($order),
            'client_secret' => $order->stripe_client_secret,
            'stripe_publishable_key' => config('services.stripe.key'),
        ]);
    }
}
