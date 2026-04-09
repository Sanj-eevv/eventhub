<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CancelPaidOrderAction;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessRefundJob;
use App\Models\Order;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

final class CancelOrderController extends Controller
{
    public function __construct(
        private readonly CancelPaidOrderAction $cancelPaidOrderAction,
        private readonly Dispatcher $dispatcher,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Order $order, Request $request): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $this->cancelPaidOrderAction->execute($order, $request->user());

        $this->dispatcher->dispatch(new ProcessRefundJob($order));

        return $this->redirector->route('dashboard.orders.show', ['order' => $order->uuid])
            ->with('toast_success', 'Order cancelled. Refund is being processed.');
    }
}
