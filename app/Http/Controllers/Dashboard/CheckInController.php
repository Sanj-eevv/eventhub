<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Enums\CheckInPermissions;
use App\Http\Controllers\Controller;
use App\Http\Resources\Event\ShowResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

final class CheckInController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request, Event $event): Response
    {
        abort_if( ! $request->user()->hasPermission(CheckInPermissions::AllowManage), 403);

        return $this->inertiaResponse->render('Dashboard/CheckIn/Index', [
            'event' => ShowResource::make($event),
        ]);
    }
}
