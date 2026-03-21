<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;

final readonly class EventData
{
    public function __construct(
        public int $user_id,
        public int $organization_id,
        public string $title,
        public string $description,
        public CarbonImmutable $starts_at,
        public ?CarbonImmutable $ends_at,
        public string $timezone,
        public ?array $location,
        /** @param TicketTypeData[] $ticket_types */
        public array $ticket_types,
    ) {}
}
