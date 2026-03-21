<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Actions\CompleteOrderAction;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

final class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly CompleteOrderAction $completeOrderAction,
    ) {}

    public function __invoke(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret'),
            );
        } catch (SignatureVerificationException) {
            return response('Unauthorized', 401);
        }

        if ('payment_intent.succeeded' !== $event->type) {
            return response('OK', 200);
        }

        $paymentIntentId = $event->data->object->id;

        try {
            $order = Order::query()
                ->where('stripe_payment_intent_id', $paymentIntentId)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return response('OK', 200);
        }

        $this->completeOrderAction->execute($order);

        return response('OK', 200);
    }
}
