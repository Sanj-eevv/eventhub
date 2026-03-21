<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Event;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class DestroyEventMediaAction
{
    public function execute(Event $event, Media $media): void
    {
        DB::transaction(function () use ($event, $media): void {
            $wasCover = $media->is_cover;

            $paths = array_filter([$media->original_path, $media->processed_path]);
            foreach ($paths as $path) {
                Storage::disk($media->disk)->delete($path);
            }

            $media->delete();

            if ($wasCover) {
                $event->media()->first()?->update(['is_cover' => true]);
            }
        });
    }
}
