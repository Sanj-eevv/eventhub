<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

final readonly class Percentage
{
    public function __construct(public int $value) {}

    public static function fromInt(int $value): self
    {
        throw_if($value < 0 || $value > 100, InvalidArgumentException::class, sprintf('Percentage must be between 0 and 100, got %d.', $value));

        return new self($value);
    }

    public function applyTo(int $amount): int
    {
        return (int) round($amount * $this->value / 100);
    }
}
