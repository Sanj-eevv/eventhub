<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\CancelPaidOrderAction;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessRefundJob;
use App\Models\Order;
use App\Models\Setting;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class CancelOrderController extends Controller
{
    public function __construct(
        private readonly CancelPaidOrderAction $cancelPaidOrderAction,
        private readonly Dispatcher $dispatcher,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $cutoffHours = (int) Setting::get('cancellation_cutoff_hours', default: 24);

        if ($order->event->starts_at->isBefore(CarbonImmutable::now()->addHours($cutoffHours))) {
            throw new HttpException(422, 'Cancellations are no longer accepted this close to the event.');
        }

        $this->cancelPaidOrderAction->execute($order);

        $this->dispatcher->dispatch(new ProcessRefundJob($order));

        return $this->redirector->route('dashboard.orders.show', ['order' => $order->uuid])
            ->with('toast_success', 'Order cancelled. Refund is being processed.');
    }
}
