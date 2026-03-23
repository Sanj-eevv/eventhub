<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Queue\Queueable;
use Intervention\Image\ImageManager;

final class ProcessEventMedia implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(private readonly Media $media) {}

    public function handle(FilesystemManager $filesystemManager): void
    {

        $disk = $filesystemManager->disk($this->media->disk);

        if ( ! $disk->exists($this->media->path)) {
            return;
        }

        $originalPath = $this->media->path;
        $baseName = pathinfo($originalPath, PATHINFO_FILENAME).'.webp';
        $processedPath = pathinfo($originalPath, PATHINFO_DIRNAME).'/'.$baseName;

        $webpContent = ImageManager::gd()->read($disk->path($originalPath))
            ->scaleDown(width: 1920)
            ->toWebp(quality: 85)
            ->toString();

        $disk->put($processedPath, $webpContent);

        $this->media->update([
            'path' => $processedPath,
            'filename' => $baseName,
            'mime_type' => 'image/webp',
            'size' => mb_strlen($webpContent),
        ]);

        if ($processedPath !== $originalPath) {
            $disk->delete($originalPath);
        }
    }
}
