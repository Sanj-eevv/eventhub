<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

final class MarkAllNotificationsReadController extends Controller
{
    public function __construct(private readonly Redirector $redirector) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return $this->redirector->back();
    }
}
