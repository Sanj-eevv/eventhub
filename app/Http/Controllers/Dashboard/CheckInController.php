<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Inertia\Response;
use Inertia\ResponseFactory;

final class CheckInController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Event $event): Response
    {
        $this->authorize('checkIn', $event);

        return $this->inertiaResponse->render('Dashboard/CheckIn/Index', [
            'event' => EventResource::make($event),
            'initialCheckedIn' => $event->tickets()->used()->count(),
            'totalTickets' => $event->tickets()->count(),
        ]);
    }
}
