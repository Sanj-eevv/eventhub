<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class ReserveTicketsData
{
    /**
     * @param  array<int, array{ticket_type_id: int, quantity: int}>  $items
     */
    public function __construct(
        public int $user_id,
        public int $event_id,
        public array $items,
    ) {}
}
