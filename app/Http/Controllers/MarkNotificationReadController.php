<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
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
        /** @var User $user */
        $user = $this->authManager->user();

        $user->notifications()
            ->findOrFail($notificationId)
            ->markAsRead();

        return $this->redirector->back();
    }
}
