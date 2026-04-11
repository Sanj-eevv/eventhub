<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Event;
use App\Models\Media;
use Illuminate\Database\DatabaseManager;

final class SetCoverMediaAction
{
    public function __construct(private readonly DatabaseManager $databaseManager) {}

    public function execute(Event $event, Media $media): void
    {
        $this->databaseManager->transaction(function () use ($event, $media): void {
            $event->media()->update(['is_cover' => false]);
            $media->update(['is_cover' => true]);
        });
    }
}
