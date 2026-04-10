<?php

declare(strict_types=1);

use App\ValueObjects\BookingReference;

it('generates a reference starting with EVT-', function (): void {
    expect((string) BookingReference::generate())->toStartWith('EVT-');
});

it('generates a reference of the correct length', function (): void {
    expect(mb_strlen((string) BookingReference::generate()))->toBe(10);
});

it('generates unique references across multiple calls', function (): void {
    $first = (string) BookingReference::generate();
    $second = (string) BookingReference::generate();

    expect($first)->not->toBe($second);
});

it('casts to string correctly', function (): void {
    $reference = BookingReference::generate();

    expect((string) $reference)->toBeString()->toStartWith('EVT-');
});
