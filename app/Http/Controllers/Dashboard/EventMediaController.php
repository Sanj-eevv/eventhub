<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\DestroyEventMediaAction;
use App\Actions\SetCoverMediaAction;
use App\Actions\StoreEventMediaAction;
use App\Http\Requests\Dashboard\StoreEventMediaRequest;
use App\Models\Event;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class EventMediaController
{
    public function __construct(
        private readonly StoreEventMediaAction $storeAction,
        private readonly DestroyEventMediaAction $destroyAction,
        private readonly SetCoverMediaAction $setCoverAction,
        private readonly Redirector $redirector,
    ) {}

    public function store(StoreEventMediaRequest $request, Event $event): RedirectResponse
    {
        $this->storeAction->execute($event, $request->file('file'));

        return $this->redirector->back();
    }

    public function destroy(Event $event, Media $media): RedirectResponse
    {
        $this->destroyAction->execute($event, $media);

        return $this->redirector->back();
    }

    public function cover(Event $event, Media $media): RedirectResponse
    {
        $this->setCoverAction->execute($event, $media);

        return $this->redirector->back();
    }
}
