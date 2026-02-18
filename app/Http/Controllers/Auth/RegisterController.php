<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class RegisterController extends Controller
{
    public function register(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $registerRequest): RedirectResponse
    {
        $validated = $registerRequest->validated();
        $userRole = Role::UserRole();
        $user = User::query()->create([
            'role_id' => $userRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        event(new Registered($user));
        Auth::login($user);
        return redirect()->intended(route('home', absolute: false));
    }
}
