<?php

declare(strict_types=1);

namespace Tests;

use App\Contracts\PaymentGateway;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Fakes\FakePaymentGateway;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(PaymentGateway::class, FakePaymentGateway::class);
    }
}
