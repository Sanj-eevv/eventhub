<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\ValueObjects\DateRange;

final readonly class EventData
{
    public function __construct(
        public int $user_id,
        public int $organization_id,
        public string $title,
        public string $description,
        public DateRange $period,
        public string $timezone,
        public string $venue_name,
        public string $address,
        public string $zip,
        public ?string $map_url,
        /** @var TicketTypeData[] $ticket_types */
        public array $ticket_types,
    ) {}
}
