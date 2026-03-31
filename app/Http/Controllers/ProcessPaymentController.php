<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CompleteOrderAction;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class ProcessPaymentController extends Controller
{
    public function __construct(
        private readonly CompleteOrderAction $completeOrderAction,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        $this->completeOrderAction->execute($order);

        return $this->redirector->route('checkout.confirmation', ['order' => $order->uuid]);
    }
}
