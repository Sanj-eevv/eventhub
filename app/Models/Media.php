<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAppUuid;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $uuid
 * @property string $mediable_type
 * @property int $mediable_id
 * @property string $disk
 * @property string $path
 * @property string $filename
 * @property string $mime_type
 * @property int $size
 * @property bool $is_cover
 * @property int $sort_order
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable([
    'uuid',
    'mediable_type',
    'mediable_id',
    'disk',
    'path',
    'filename',
    'mime_type',
    'size',
    'is_cover',
    'sort_order',
])]
final class Media extends Model
{
    use HasAppUuid;

    /** @return MorphTo<Model, $this> */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
        ];
    }
}
