<?php

declare(strict_types=1);

use App\Exceptions\ActiveReservationExistsException;
use App\Exceptions\InsufficientTicketCapacityException;
use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\MediaLimitExceededException;
use App\Exceptions\MissingEventCoverImageException;
use App\Exceptions\TicketLimitExceededException;
use App\Exceptions\TicketSaleClosedException;
use App\Exceptions\TicketSaleNotOpenException;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LoadPermissionsMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('auth.login'));

        $middleware->validateCsrfTokens(except: [
            'webhooks/stripe',
        ]);

        $middleware->web([
            LoadPermissionsMiddleware::class,
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(fn (ActiveReservationExistsException $exception) => back()->with('toast_error', $exception->getMessage()));
        $exceptions->render(fn (InvalidStatusTransitionException $exception) => back()->with('toast_error', $exception->getMessage()));
        $exceptions->render(fn (InsufficientTicketCapacityException $exception) => back()->with('toast_error', $exception->getMessage()));
        $exceptions->render(fn (TicketLimitExceededException $exception) => back()->with('toast_error', $exception->getMessage()));
        $exceptions->render(fn (MediaLimitExceededException $exception) => back()->with('toast_error', $exception->getMessage()));
        $exceptions->render(fn (MissingEventCoverImageException $exception) => back()->with('toast_error', $exception->getMessage()));
        $exceptions->render(fn (TicketSaleNotOpenException $exception) => back()->with('toast_error', $exception->getMessage()));
        $exceptions->render(fn (TicketSaleClosedException $exception) => back()->with('toast_error', $exception->getMessage()));
    })->create();
