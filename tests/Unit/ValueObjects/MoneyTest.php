<?php

declare(strict_types=1);

use App\ValueObjects\Money;
use App\ValueObjects\Percentage;

it('creates an instance from cents with uppercase currency', function (): void {
    $money = Money::fromCents(1000, 'usd');

    expect($money->cents)->toBe(1000)
        ->and($money->currency)->toBe('USD');
});

it('formats the amount as a decimal string with currency', function (): void {
    expect(Money::fromCents(12345, 'USD')->format())->toBe('123.45 USD');
});

it('formats zero cents', function (): void {
    expect(Money::fromCents(0, 'USD')->format())->toBe('0.00 USD');
});

it('applies 100 percent and returns the same amount', function (): void {
    $money = Money::fromCents(5000, 'USD');

    expect($money->applyPercentage(Percentage::fromInt(100))->cents)->toBe(5000);
});

it('applies 50 percent and halves the amount', function (): void {
    $money = Money::fromCents(5000, 'USD');

    expect($money->applyPercentage(Percentage::fromInt(50))->cents)->toBe(2500);
});

it('applies 0 percent and returns zero', function (): void {
    $money = Money::fromCents(5000, 'USD');

    expect($money->applyPercentage(Percentage::fromInt(0))->cents)->toBe(0);
});

it('preserves currency after applying percentage', function (): void {
    $money = Money::fromCents(5000, 'EUR');

    expect($money->applyPercentage(Percentage::fromInt(50))->currency)->toBe('EUR');
});
