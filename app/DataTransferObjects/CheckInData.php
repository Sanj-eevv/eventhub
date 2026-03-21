<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class CheckInData
{
    public function __construct(
        public string $booking_reference,
        public int $checked_in_by_user_id,
    ) {}
}
