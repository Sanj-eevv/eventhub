<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Resources\SharedPermissionResource;
use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    /**
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || 'true' === $request->cookie('sidebar_state'),
            'flash' => [
                'toastSuccess' => fn () => $request->session()->get('toastSuccess'),
                'toastWarning' => fn () => $request->session()->get('toast.warning'),
                'toastInfo' => fn () => $request->session()->get('toast.info'),
                'toastError' => fn () => $request->session()->get('toast.error'),
            ],
            'can' => SharedPermissionResource::make($request->user()),

        ];
    }
}
