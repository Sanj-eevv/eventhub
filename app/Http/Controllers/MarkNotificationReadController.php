<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class MarkNotificationReadController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(string $notificationId): RedirectResponse
    {
        $this->authManager->user()
            ->notifications()
            ->findOrFail($notificationId)
            ->markAsRead();

        return $this->redirector->back();
    }
}
