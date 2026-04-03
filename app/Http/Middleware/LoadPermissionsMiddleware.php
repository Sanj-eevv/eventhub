<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadPermissionsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $request->user()->load('role.permissions');
        }

        return $next($request);
    }
}
