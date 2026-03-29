<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class TicketItemData
{
    public function __construct(
        public string $ticketTypeUuid,
        public int $quantity,
    ) {}
}
