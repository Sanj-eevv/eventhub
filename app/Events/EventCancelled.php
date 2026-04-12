<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class EventCancelled
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Event $event) {}
}
