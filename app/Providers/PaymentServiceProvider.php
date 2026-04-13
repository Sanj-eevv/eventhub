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
        $this->app->singleton(StripeClient::class, fn (): StripeClient => new StripeClient((string) config('services.stripe.secret', '')));

        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
    }
}
