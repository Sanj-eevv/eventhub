<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActivityEvent;
use App\Traits\HasAppUuid;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $uuid
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property ActivityEvent $event
 * @property array<string, mixed>|null $properties
 * @property CarbonImmutable $created_at
 * @property-read Model|null $causer
 * @property-read Model|null $subject
 */
#[Fillable([
    'causer_type',
    'causer_id',
    'subject_type',
    'subject_id',
    'event',
    'properties',
])]
final class ActivityLog extends Model
{
    use HasAppUuid;

    public $timestamps = false;

    /** @return MorphTo<Model, $this> */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return MorphTo<Model, $this> */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'event' => ActivityEvent::class,
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
