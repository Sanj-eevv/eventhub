<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class MediaLimitExceededException extends RuntimeException
{
    public function __construct(int $limit)
    {
        parent::__construct(sprintf('Media limit of %d files per event has been reached.', $limit));
    }
}
