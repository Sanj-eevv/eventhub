<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\MediaLimitExceededException;
use App\Jobs\ProcessEventMedia;
use App\Models\Event;
use App\Models\Media;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Throwable;

final class StoreEventMediaAction
{
    private const int MAX_FILES = 10;

    public function __construct(
        private readonly FilesystemManager $filesystem,
        private readonly DatabaseManager $databaseManager,
        private readonly Dispatcher $dispatcher,
        private readonly Config $config,
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(Event $event, UploadedFile $file): Media
    {
        $uuid = Str::uuid()->toString();
        $filename = $file->getClientOriginalName();
        $extension = $file->extension();
        $originalPath = "media/{$event->uuid}/{$uuid}.{$extension}";

        $this->filesystem->writeStream($originalPath, fopen($file->getRealPath(), 'r'));

        try {
            $this->databaseManager->beginTransaction();
            $count = $event->media()->lockForUpdate()->count();

            if ($count >= self::MAX_FILES) {
                throw new MediaLimitExceededException(self::MAX_FILES);
            }

            $media = $event->media()->create([
                'uuid' => $uuid,
                'disk' => $this->config->get('filesystems.default'),
                'path' => $originalPath,
                'filename' => $filename,
                'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                'size' => $file->getSize(),
                'is_cover' => 0 === $count,
                'sort_order' => $count,
            ]);
            $this->dispatcher->dispatch(new ProcessEventMedia($media));
            $this->databaseManager->commit();

            return $media;
        } catch (Throwable $exception) {
            $this->databaseManager->rollBack();

            if ( ! $this->filesystem->delete($originalPath)) {
                $this->logger->warning('Failed to delete orphaned media file after transaction rollback.', [
                    'path' => $originalPath,
                ]);
            }

            throw $exception;
        }
    }
}
