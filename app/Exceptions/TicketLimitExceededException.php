<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\TicketType;
use RuntimeException;

final class TicketLimitExceededException extends RuntimeException
{
    public function __construct(TicketType $ticketType)
    {
        parent::__construct(sprintf('You have reached the maximum of [%s] tickets for [%s].', $ticketType->max_per_user, $ticketType->name));
    }
}
