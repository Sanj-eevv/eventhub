<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Event\ShowResource;
use App\Models\Event;
use Inertia\Response;
use Inertia\ResponseFactory;

final class IndexController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function __invoke(): Response
    {
        $events = Event::query()
            ->published()
            ->with('media')
            ->orderBy('starts_at')
            ->limit(9)
            ->get();

        return $this->inertiaResponse->render('Home', [
            'events' => ShowResource::collection($events),
        ]);
    }
}
