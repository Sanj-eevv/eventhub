<?php

declare(strict_types=1);

namespace App\Exceptions;

use BackedEnum;
use RuntimeException;

final class InvalidStatusTransitionException extends RuntimeException
{
    public function __construct(BackedEnum $from, BackedEnum $to)
    {
        parent::__construct(sprintf('Cannot transition from [%s] to [%s].', $from->value, $to->value));
    }
}
