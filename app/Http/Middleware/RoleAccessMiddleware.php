<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class RoleAccessMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if ( ! Auth::check()) {
            return redirect()->route('auth.login');
        }
        if ( ! Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized action');
        }

        return $next($request);
    }
}
