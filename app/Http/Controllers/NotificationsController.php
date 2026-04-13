<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

final class NotificationsController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $this->authManager->user();

        $notifications = $user
            ->notifications()
            ->latest()
            ->paginate(perPage: 15, page: $request->integer('page', 1));

        return $this->inertiaResponse->render('My/Notifications/Index', [
            'notifications' => $this->inertiaResponse->scroll(NotificationResource::collection($notifications)),
        ]);
    }
}
