<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

final readonly class DateRange
{
    public function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
    ) {
        if ($end->isBefore($start)) {
            throw new InvalidArgumentException('End date must be after start date.');
        }
    }

    public function contains(CarbonImmutable $date): bool
    {
        return $date->isBetween($this->start, $this->end);
    }
}
