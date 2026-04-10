<?php

declare(strict_types=1);

namespace App\Enums;

enum OrganizationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Suspended => 'Suspended',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'blue',
            self::Approved => 'green',
            self::Rejected => 'yellow',
            self::Suspended => 'red',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Rejected, self::Suspended], true);
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::Pending => in_array($status, [self::Approved, self::Rejected], true),
            self::Approved => self::Suspended === $status,
            self::Rejected, self::Suspended => false,
        };
    }
}
