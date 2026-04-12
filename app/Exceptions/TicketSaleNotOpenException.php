<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\TicketType;
use RuntimeException;

final class TicketSaleNotOpenException extends RuntimeException
{
    public function __construct(TicketType $ticketType)
    {
        parent::__construct(sprintf('Ticket sales for [%s] have not opened yet.', $ticketType->name));
    }
}
