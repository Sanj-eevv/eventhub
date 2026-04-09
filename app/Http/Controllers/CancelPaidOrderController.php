<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CancelPaidOrderAction;
use App\Jobs\ProcessRefundJob;
use App\Models\Order;
use App\Services\SettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class CancelPaidOrderController extends Controller
{
    public function __construct(
        private readonly CancelPaidOrderAction $cancelPaidOrderAction,
        private readonly Dispatcher $dispatcher,
        private readonly Redirector $redirector,
        private readonly SettingsService $settingsService,
    ) {}

    public function __invoke(Order $order, Request $request): RedirectResponse
    {
        $this->authorize('cancel', $order);

        if ($order->event->starts_at->isBefore(CarbonImmutable::now()->addHours($this->settingsService->get()->cancellationCutoffHours))) {
            throw new HttpException(422, 'The cancellation window for this order has passed.');
        }

        $this->cancelPaidOrderAction->execute($order, $request->user());

        $this->dispatcher->dispatch(new ProcessRefundJob($order));

        return $this->redirector->route('orders.show', ['order' => $order->uuid])
            ->with('toast_success', 'Your order has been cancelled. Your refund is being processed.');
    }
}
