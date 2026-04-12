<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Inertia\Response;
use Inertia\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CheckoutConfirmationController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function __invoke(Order $order): Response
    {
        $this->authorize('view', $order);

        throw_unless(in_array($order->status, [OrderStatus::Reserved, OrderStatus::Paid], true), NotFoundHttpException::class);

        $order->load(['tickets.ticketType', 'event']);

        return $this->inertiaResponse->render('Checkout/Confirmation', [
            'order' => OrderResource::make($order),
        ]);
    }
}
