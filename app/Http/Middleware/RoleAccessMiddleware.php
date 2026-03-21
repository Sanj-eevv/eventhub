<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;

final class RoleAccessMiddleware
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly Redirector $redirector,
    ) {}

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if ( ! $this->authManager->check()) {
            return $this->redirector->route('auth.login');
        }

        if ( ! $this->authManager->user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized action');
        }

        return $next($request);
    }
}
