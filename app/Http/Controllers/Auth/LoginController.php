<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class LoginController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $loginRequest): RedirectResponse
    {
        if (Auth::attempt($loginRequest->validated(), $loginRequest->boolean('remember'))) {
            $loginRequest->session()->regenerate();
            return redirect()->intended(route('home', absolute: false));
        }
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput($loginRequest->except('password'));
    }
}
