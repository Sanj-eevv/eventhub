<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Support\Str;

final readonly class BookingReference
{
    private function __construct(public string $value) {}

    public function __toString(): string
    {
        return $this->value;
    }

    public static function generate(): self
    {
        return new self('EVT-'.Str::of(Str::random(6))->upper());
    }
}
