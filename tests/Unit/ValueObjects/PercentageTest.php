<?php

declare(strict_types=1);

use App\ValueObjects\Percentage;

it('accepts 0', function (): void {
    expect(Percentage::fromInt(0)->value)->toBe(0);
});

it('accepts 100', function (): void {
    expect(Percentage::fromInt(100)->value)->toBe(100);
});

it('throws for a value below 0', function (): void {
    expect(fn (): Percentage => Percentage::fromInt(-1))->toThrow(InvalidArgumentException::class);
});

it('throws for a value above 100', function (): void {
    expect(fn (): Percentage => Percentage::fromInt(101))->toThrow(InvalidArgumentException::class);
});

it('applies to an amount correctly', function (): void {
    expect(Percentage::fromInt(50)->applyTo(1000))->toBe(500);
});

it('applies to an amount and rounds the result', function (): void {
    expect(Percentage::fromInt(33)->applyTo(100))->toBe(33);
});

it('applies 0 percent to return zero', function (): void {
    expect(Percentage::fromInt(0)->applyTo(1000))->toBe(0);
});

it('applies 100 percent to return the full amount', function (): void {
    expect(Percentage::fromInt(100)->applyTo(1000))->toBe(1000);
});
