<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use UnitEnum;

final class InvalidStatusTransitionException extends RuntimeException
{
    public function __construct(UnitEnum $from, UnitEnum $to)
    {
        parent::__construct(sprintf('Cannot transition from [%s] to [%s].', $from->value, $to->value));
    }
}
