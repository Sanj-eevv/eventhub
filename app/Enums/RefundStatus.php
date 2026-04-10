<?php

declare(strict_types=1);

namespace App\Enums;

enum RefundStatus: string
{
    case Pending = 'pending';
    case Refunded = 'refunded';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Refunded => 'Refunded',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'blue',
            self::Refunded => 'green',
            self::Failed => 'red',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Refunded, self::Failed], true);
    }
}
