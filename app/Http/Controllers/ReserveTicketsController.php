<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreatePaymentIntentAction;
use App\Actions\ReserveTicketsAction;
use App\Exceptions\InsufficientTicketCapacityException;
use App\Exceptions\TicketLimitExceededException;
use App\Http\Requests\ReserveTicketsRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class ReserveTicketsController extends Controller
{
    public function __construct(
        private readonly ReserveTicketsAction $reserveTicketsAction,
        private readonly CreatePaymentIntentAction $createPaymentIntentAction,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(ReserveTicketsRequest $request, Event $event): RedirectResponse
    {
        dd($request->validated('items'));
        try {
            $order = $this->reserveTicketsAction->execute(
                $request->user(),
                $event,
                $request->validated('items'),
            );

            $this->createPaymentIntentAction->execute($order);

            return $this->redirector->route('checkout.show', ['order' => $order->uuid]);
        } catch (InsufficientTicketCapacityException $exception) {
            return $this->redirector->back()->with('toast_error', $exception->getMessage());
        } catch (TicketLimitExceededException $exception) {
            return $this->redirector->back()->with('toast_error', $exception->getMessage());
        }
    }
}
