<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\UpdateEventStatusAction;
use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class CancelEventController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly UpdateEventStatusAction $updateEventStatusAction,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Event $event): RedirectResponse
    {
        $this->authorize('cancel', $event);

        $this->updateEventStatusAction->execute($event, EventStatus::Cancelled, $this->authManager->user());

        return $this->redirector->back()->with('toast_success', 'Event cancelled.');
    }
}
