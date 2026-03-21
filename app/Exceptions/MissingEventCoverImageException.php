<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class MissingEventCoverImageException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('A cover image is required before publishing an event.');
    }
}
