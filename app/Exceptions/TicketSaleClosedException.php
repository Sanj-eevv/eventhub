<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\TicketType;
use RuntimeException;

final class TicketSaleClosedException extends RuntimeException
{
    public function __construct(TicketType $ticketType)
    {
        parent::__construct("Ticket sales for [{$ticketType->name}] have closed.");
    }
}
