<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class CheckoutController extends Controller
{
    public function __construct(
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function show(Request $request, Order $order): Response|RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if (OrderStatus::Reserved !== $order->status) {
            return $this->redirector->route('events.show', ['event' => $order->event->slug]);
        }

        $order->load(['tickets.ticketType', 'event']);

        return $this->inertiaResponse->render('Checkout/Show', [
            'order' => OrderResource::make($order),
            'expires_at' => $order->expires_at,
            'stripe_publishable_key' => config('services.stripe.key'),
        ]);
    }

    public function confirmation(Request $request, Order $order): Response
    {
        abort_if($order->user_id !== $request->user()->id, 403);
        abort_if(OrderStatus::Paid !== $order->status, 403);

        $order->load(['tickets.ticketType', 'event']);

        return $this->inertiaResponse->render('Checkout/Confirmation', [
            'order' => OrderResource::make($order),
        ]);
    }
}
