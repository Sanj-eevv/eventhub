<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Cancelled = 'cancelled';
    case Used = 'used';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Active => 'Active',
            self::Cancelled => 'Cancelled',
            self::Used => 'Used',
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::Pending => in_array($status, [self::Active, self::Cancelled], true),
            self::Active => in_array($status, [self::Used, self::Cancelled], true),
            self::Used, self::Cancelled => false,
        };
    }
}
