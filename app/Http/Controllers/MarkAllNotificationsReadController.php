<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class MarkAllNotificationsReadController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(): RedirectResponse
    {
        $this->authManager->user()->unreadNotifications()->update(['read_at' => now()]);

        return $this->redirector->back();
    }
}
