<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

final class MarkNotificationReadController extends Controller
{
    public function __construct(private readonly Redirector $redirector) {}

    public function __invoke(Request $request, string $notificationId): RedirectResponse
    {
        $request->user()
            ->notifications()
            ->findOrFail($notificationId)
            ->markAsRead();

        return $this->redirector->back();
    }
}
