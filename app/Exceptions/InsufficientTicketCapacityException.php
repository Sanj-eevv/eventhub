<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\TicketType;
use RuntimeException;

final class InsufficientTicketCapacityException extends RuntimeException
{
    public function __construct(TicketType $ticketType)
    {
        parent::__construct(sprintf('Not enough capacity available for ticket type [%s].', $ticketType->name));
    }
}
