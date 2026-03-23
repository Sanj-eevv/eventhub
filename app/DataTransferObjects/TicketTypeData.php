<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;

final readonly class TicketTypeData
{
    public function __construct(
        public string $name,
        public int $price,
        public int $capacity,
        public ?int $max_per_user,
        public int $sort_order,
        public ?string $uuid = null,
        public ?string $description = null,
        public bool $is_active = true,
        public ?CarbonImmutable $sale_starts_at = null,
        public ?CarbonImmutable $sale_ends_at = null,
    ) {}
}
