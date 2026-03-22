<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\SetCoverMediaAction;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class SetEventMediaCoverController extends Controller
{
    public function __construct(
        private readonly SetCoverMediaAction $setCoverAction,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(Event $event, Media $media): RedirectResponse
    {
        $this->authorize('update', $event);

        $this->setCoverAction->execute($event, $media);

        return $this->redirector->back()->with('toast_success', 'Cover image updated successfully.');
    }
}
