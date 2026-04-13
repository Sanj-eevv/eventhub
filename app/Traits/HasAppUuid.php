<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

trait HasAppUuid
{
    use HasUuids;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /** @return string[] */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}
