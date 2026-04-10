<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class ProcessPaymentController extends Controller
{
    public function __construct(
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        return $this->redirector->route('checkout.confirmation', ['order' => $order->uuid]);
    }
}
