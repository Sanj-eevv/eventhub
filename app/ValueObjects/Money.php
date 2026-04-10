<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Support\Number;
use Illuminate\Support\Str;

final readonly class Money
{
    private function __construct(
        public int $cents,
        public string $currency,
    ) {}

    public static function fromCents(int $cents, string $currency): self
    {
        return new self($cents, Str::upper($currency));
    }

    public function format(): string
    {
        return Number::format($this->cents / 100, 2).' '.$this->currency;
    }

    public function applyPercentage(Percentage $percentage): self
    {
        return new self((int) round($this->cents * $percentage->value / 100), $this->currency);
    }
}
