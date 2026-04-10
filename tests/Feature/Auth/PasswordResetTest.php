<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('renders the forgot password page', function (): void {
    $this->get(route('auth.password.request'))->assertSuccessful();
});

it('sends a password reset link to a known email', function (): void {
    Notification::fake();

    $user = User::factory()->create(['role_id' => Role::userRole()->id]);

    $this->post(route('auth.password.email'), ['email' => $user->email])->assertRedirect();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('does not expose whether an email exists when requesting a reset', function (): void {
    $this->post(route('auth.password.email'), ['email' => 'nobody@example.com'])
        ->assertSessionMissing('errors');
});

it('renders the reset password form for a valid token', function (): void {
    $user = User::factory()->create(['role_id' => Role::userRole()->id]);
    $token = Password::createToken($user);

    $this->get(route('auth.password.reset', ['token' => $token]))->assertSuccessful();
});

it('resets the password with a valid token', function (): void {
    Notification::fake();

    $user = User::factory()->create([
        'role_id' => Role::userRole()->id,
        'password' => bcrypt('OldPassword1!'),
    ]);

    $token = Password::createToken($user);

    $this->post(route('auth.password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertRedirect();

    expect(Hash::check('NewPassword1!', $user->fresh()->password))->toBeTrue();
});

it('requires password confirmation when resetting', function (): void {
    $user = User::factory()->create(['role_id' => Role::userRole()->id]);
    $token = Password::createToken($user);

    $this->post(route('auth.password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'wrong',
    ])->assertSessionHasErrors('password');
});
