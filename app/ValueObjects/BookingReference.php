<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Models\Ticket;
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
        do {
            $reference = 'EVT-'.mb_strtoupper(Str::random(6));
        } while (Ticket::query()->where('booking_reference', $reference)->exists());

        return new self($reference);
    }
}
