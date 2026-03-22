<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\UpdateEventStatusAction;
use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class PublishEventController extends Controller
{
    public function __construct(
        private readonly UpdateEventStatusAction $updateEventStatusAction,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Event $event): RedirectResponse
    {
        $this->authorize('publish', $event);

        $this->updateEventStatusAction->execute($event, EventStatus::Published);

        return $this->redirector->back()->with('toast_success', 'Event published successfully.');
    }
}
