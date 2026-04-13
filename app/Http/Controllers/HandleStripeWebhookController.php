<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\HandleStripeWebhookAction;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

final class HandleStripeWebhookController extends Controller
{
    public function __construct(
        private readonly HandleStripeWebhookAction $handleStripeWebhookAction,
        private readonly Config $config,
        private readonly ResponseFactory $responseFactory,
    ) {}

    public function __invoke(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                (string) ($this->config->get('services.stripe.webhook_secret', '')),
            );
        } catch (SignatureVerificationException) {
            abort(400);
        }

        ($this->handleStripeWebhookAction)($event);

        return $this->responseFactory->noContent();
    }
}
