<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

final class LoadPermisssionsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            Context::addHidden('permissions', Auth::user()->getAllPermissions());
        }

        return $next($request);
    }
}
