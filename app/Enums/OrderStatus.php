<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Reserved = 'reserved';
    case Paid = 'paid';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Reserved => 'Reserved',
            self::Paid => 'Paid',
            self::Expired => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::Reserved => in_array($status, [self::Paid, self::Expired, self::Cancelled], true),
            self::Paid => self::Cancelled === $status,
            self::Expired, self::Cancelled => false,
        };
    }
}
