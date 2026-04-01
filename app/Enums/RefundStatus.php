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
}
