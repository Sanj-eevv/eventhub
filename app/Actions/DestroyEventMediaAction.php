<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Event;
use App\Models\Media;
use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\FilesystemManager;

final readonly class DestroyEventMediaAction
{
    public function __construct(
        private FilesystemManager $filesystemManager,
        private DatabaseManager $databaseManager,
    ) {}

    public function execute(Event $event, Media $media): void
    {
        $this->databaseManager->transaction(function () use ($event, $media): void {
            $wasCover = $media->is_cover;

            $this->filesystemManager->disk($media->disk)->delete($media->path);

            $media->delete();

            if ($wasCover) {
                $event->media()->first()?->update(['is_cover' => true]);
            }
        });
    }
}
