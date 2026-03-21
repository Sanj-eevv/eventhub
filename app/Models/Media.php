<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAppUuid;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $uuid
 * @property string $mediable_type
 * @property int $mediable_id
 * @property string $disk
 * @property string $original_path
 * @property string|null $processed_path
 * @property string $filename
 * @property string $mime_type
 * @property int $size
 * @property bool $is_cover
 * @property int $sort_order
 * @property CarbonImmutable|null $processed_at
 * @property CarbonImmutable|null $processing_failed_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class Media extends Model
{
    use HasAppUuid;

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'disk',
        'original_path',
        'processed_path',
        'filename',
        'mime_type',
        'size',
        'is_cover',
        'sort_order',
        'processed_at',
        'processing_failed_at',
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        $path = $this->processed_path ?? $this->original_path;

        return Storage::disk($this->disk)->url($path);
    }

    public function originalUrl(): string
    {
        return Storage::disk($this->disk)->url($this->original_path);
    }

    public function isProcessing(): bool
    {
        return null === $this->processed_at && null === $this->processing_failed_at;
    }

    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
            'processed_at' => 'datetime',
            'processing_failed_at' => 'datetime',
        ];
    }
}
