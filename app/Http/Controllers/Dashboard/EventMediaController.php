<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\DestroyEventMediaAction;
use App\Actions\StoreEventMediaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreEventMediaRequest;
use App\Models\Event;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class EventMediaController extends Controller
{
    public function __construct(
        private readonly StoreEventMediaAction $storeAction,
        private readonly DestroyEventMediaAction $destroyAction,
        private readonly Redirector $redirector,
    ) {}

    public function store(StoreEventMediaRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        ($this->storeAction)($event, $request->file('file'));

        return $this->redirector->back()->with('toast_success', 'Media uploaded successfully.');
    }

    public function destroy(Event $event, Media $media): RedirectResponse
    {
        $this->authorize('update', $event);

        ($this->destroyAction)($event, $media);

        return $this->redirector->back()->with('toast_success', 'Media deleted.');
    }
}
