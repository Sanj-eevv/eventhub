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
use Illuminate\Support\Facades\Hash;
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
        $defaultRole = Role::defaultRole();
        $user = User::query()->create([
            'role_id' => $defaultRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        event(new Registered($user));
        Auth::login($user);
        return redirect()->intended(route('dashboard.index', absolute: false));
    }
}
