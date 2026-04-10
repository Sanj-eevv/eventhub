<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class EventNotAvailableException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('This event is not available for ticket reservations.');
    }
}
