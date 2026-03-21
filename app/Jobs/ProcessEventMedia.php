<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

final class ProcessEventMedia implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(private readonly Media $media) {}

    public function handle(): void
    {
        $disk = Storage::disk($this->media->disk);

        if ( ! $disk->exists($this->media->original_path)) {
            return;
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($disk->path($this->media->original_path));

        $image->scaleDown(width: 1920);

        $processedPath = 'media/'.dirname($this->media->original_path).'/'.pathinfo($this->media->filename, PATHINFO_FILENAME).'_processed.webp';

        $disk->put($processedPath, $image->toWebp(quality: 85)->toString());

        $this->media->update([
            'processed_path' => $processedPath,
            'processed_at' => now(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->media->update(['processing_failed_at' => now()]);
    }
}
