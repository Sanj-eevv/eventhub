<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\UpdateEventStatusAction;
use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

final class PublishEventController extends Controller
{
    public function __construct(
        private readonly UpdateEventStatusAction $updateEventStatusAction,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Event $event, Request $request): RedirectResponse
    {
        $this->authorize('publish', $event);

        $this->updateEventStatusAction->execute($event, EventStatus::Published, $request->user());

        return $this->redirector->back()->with('toast_success', 'Event published successfully.');
    }
}
