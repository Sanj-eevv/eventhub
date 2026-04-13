<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Payments\StripePaymentGateway;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

final class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function (): StripeClient {
            $secret = config('services.stripe.secret', '');

            return new StripeClient(is_string($secret) ? $secret : '');
        });

        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
    }
}
