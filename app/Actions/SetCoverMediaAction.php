<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Event;
use App\Models\Media;
use Illuminate\Support\Facades\DB;

final class SetCoverMediaAction
{
    public function execute(Event $event, Media $media): void
    {
        DB::transaction(function () use ($event, $media): void {
            $event->media()->update(['is_cover' => false]);
            $media->update(['is_cover' => true]);
        });
    }
}
