<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Resources\SharedPermissionResource;
use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    /**
     * @var string
     */
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'role' => fn () => $request->user()?->role->only(['name', 'slug']),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || 'true' === $request->cookie('sidebar_state'),
            'flash' => [
                'toast_success' => fn () => $request->session()->get('toast_success'),
                'toast_warning' => fn () => $request->session()->get('toast_warning'),
                'toast_info' => fn () => $request->session()->get('toast_info'),
                'toast_error' => fn () => $request->session()->get('toast_error'),
            ],
            'can' => fn () => $request->user() ? SharedPermissionResource::make($request->user())->toArray() : null,

        ];
    }
}
