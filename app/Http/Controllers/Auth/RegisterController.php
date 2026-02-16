<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

final class RegisterController extends Controller
{
    public function register(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(): void {}
}
