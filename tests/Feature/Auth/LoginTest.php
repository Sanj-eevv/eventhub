<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

it('renders the login page for guests', function (): void {
    $this->get(route('auth.login'))->assertSuccessful();
});

it('redirects authenticated users away from the login page', function (): void {
    $this->actingAs(User::factory()->create(['role_id' => Role::userRole()->id]))
        ->get(route('auth.login'))
        ->assertRedirect();
});

it('logs in a user with correct credentials', function (): void {
    $user = User::factory()->create([
        'role_id' => Role::userRole()->id,
        'password' => bcrypt('Password1!'),
    ]);

    $this->post(route('auth.login.store'), [
        'email' => $user->email,
        'password' => 'Password1!',
    ])->assertRedirect();

    $this->assertAuthenticatedAs($user);
});

it('rejects login with a wrong password', function (): void {
    $user = User::factory()->create([
        'role_id' => Role::userRole()->id,
        'password' => bcrypt('correct-password'),
    ]);

    $this->post(route('auth.login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects login with an unknown email', function (): void {
    $this->post(route('auth.login.store'), [
        'email' => 'nobody@example.com',
        'password' => 'Password1!',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});
