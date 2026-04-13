<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Event;

final class DeleteEventAction
{
    public function __invoke(Event $event): void
    {
        $event->delete();
    }
}
