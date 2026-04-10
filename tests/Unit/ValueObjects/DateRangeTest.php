<?php

declare(strict_types=1);

use App\ValueObjects\DateRange;
use Carbon\CarbonImmutable;

it('throws when end date is before start date', function (): void {
    expect(fn () => new DateRange(
        CarbonImmutable::parse('2025-06-01'),
        CarbonImmutable::parse('2025-05-01'),
    ))->toThrow(InvalidArgumentException::class);
});

it('constructs successfully when end is after start', function (): void {
    $range = new DateRange(
        CarbonImmutable::parse('2025-05-01'),
        CarbonImmutable::parse('2025-06-01'),
    );

    expect($range->start->toDateString())->toBe('2025-05-01')
        ->and($range->end->toDateString())->toBe('2025-06-01');
});

it('constructs successfully when end equals start', function (): void {
    $date = CarbonImmutable::parse('2025-05-01');

    expect(fn () => new DateRange($date, $date))->not->toThrow(InvalidArgumentException::class);
});

it('contains a date within the range', function (): void {
    $range = new DateRange(
        CarbonImmutable::parse('2025-05-01'),
        CarbonImmutable::parse('2025-07-01'),
    );

    expect($range->contains(CarbonImmutable::parse('2025-06-01')))->toBeTrue();
});

it('does not contain a date before the range', function (): void {
    $range = new DateRange(
        CarbonImmutable::parse('2025-05-01'),
        CarbonImmutable::parse('2025-07-01'),
    );

    expect($range->contains(CarbonImmutable::parse('2025-04-01')))->toBeFalse();
});

it('does not contain a date after the range', function (): void {
    $range = new DateRange(
        CarbonImmutable::parse('2025-05-01'),
        CarbonImmutable::parse('2025-07-01'),
    );

    expect($range->contains(CarbonImmutable::parse('2025-08-01')))->toBeFalse();
});
