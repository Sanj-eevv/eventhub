<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreatePaymentIntentAction;
use App\Actions\ReserveTicketsAction;
use App\Http\Requests\ReserveTicketsRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class ReserveTicketsController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly CreatePaymentIntentAction $createPaymentIntentAction,
        private readonly Redirector $redirector,
        private readonly ReserveTicketsAction $reserveTicketsAction,
    ) {}

    public function __invoke(ReserveTicketsRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('reserve', $event);

        /** @var User $user */
        $user = $this->authManager->user();

        $order = ($this->reserveTicketsAction)($user, $event, $request->toDto());
        ($this->createPaymentIntentAction)($order);

        return $this->redirector->route('checkout.show', ['order' => $order->uuid]);
    }
}
