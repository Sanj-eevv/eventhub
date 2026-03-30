<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class ActiveReservationExistsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('You already have an active reservation for this event.');
    }
}
