<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

final class RegisterController extends Controller
{
    public function register()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(): void {}
}
