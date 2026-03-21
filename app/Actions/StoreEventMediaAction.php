<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\MediaLimitExceededException;
use App\Jobs\ProcessEventMedia;
use App\Models\Event;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class StoreEventMediaAction
{
    private const int MAX_FILES = 10;

    private const string DISK = 'public';

    public function execute(Event $event, UploadedFile $file): Media
    {
        return DB::transaction(function () use ($event, $file): Media {
            $count = $event->media()->lockForUpdate()->count();

            if ($count >= self::MAX_FILES) {
                throw new MediaLimitExceededException(self::MAX_FILES);
            }

            $uuid = Str::uuid()->toString();
            $extension = $file->extension();
            $filename = $file->getClientOriginalName();
            $originalPath = "media/{$event->uuid}/{$uuid}/original.{$extension}";

            Storage::disk(self::DISK)->putFileAs(
                "media/{$event->uuid}/{$uuid}",
                $file,
                "original.{$extension}",
            );

            $isFirstImage = 0 === $count;

            $media = $event->media()->create([
                'uuid' => $uuid,
                'disk' => self::DISK,
                'original_path' => $originalPath,
                'filename' => $filename,
                'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                'size' => $file->getSize(),
                'is_cover' => $isFirstImage,
                'sort_order' => $count,
            ]);

            ProcessEventMedia::dispatch($media);

            return $media;
        });
    }
}
