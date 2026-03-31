<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CancelReservedOrderAction;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class CancelOrderController extends Controller
{
    public function __construct(
        private readonly CancelReservedOrderAction $cancelReservedOrderAction,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $this->cancelReservedOrderAction->execute($order);

        return $this->redirector->route('events.show', ['event' => $order->event->slug])
            ->with('toastSuccess', 'Your reservation has been cancelled.');
    }
}
