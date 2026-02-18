<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Organization\RegisterRequest;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class RegisterController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Organization/RegisterForm/Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = DB::transaction(function () use ($validated): User {
            $organization = Organization::query()->create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'contact_address' => $validated['contact_address'],
                'contact_email' => $validated['contact_email'],
            ]);
            $role = Role::organizationAdmin();
            return User::query()->create([
                'role_id' => $role->id,
                'organization_id' => $organization->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        });
        event(new Registered($user));
        Auth::login($user);
        return redirect()->intended(route('dashboard.index', absolute: false));
    }
}
