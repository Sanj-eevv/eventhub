<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;

final readonly class TicketTypeData
{
    public function __construct(
        public string $name,
        public string $description,
        public int $price,
        public int $capacity,
        public int $sort_order,
        public bool $is_active,
        public string $uuid,
        public ?int $max_per_user,
        public ?CarbonImmutable $sale_starts_at,
        public ?CarbonImmutable $sale_ends_at
    ) {}
}
