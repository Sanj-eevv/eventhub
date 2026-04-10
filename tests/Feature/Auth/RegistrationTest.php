<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Notifications\QueueableVerifyEmail;
use Illuminate\Support\Facades\Notification;

it('renders the registration page for guests', function (): void {
    $this->get(route('auth.register'))->assertSuccessful();
});

it('redirects authenticated users away from the registration page', function (): void {
    $this->actingAs(User::factory()->create(['role_id' => Role::userRole()->id]))
        ->get(route('auth.register'))
        ->assertRedirect();
});

it('registers a user with valid data', function (): void {
    Notification::fake();

    $this->post(route('auth.register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    $this->assertAuthenticated();
});

it('assigns the User role on registration', function (): void {
    Notification::fake();

    $this->post(route('auth.register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::query()->where('email', 'jane@example.com')->firstOrFail();

    expect($user->role->slug)->toBe('user');
});

it('sends an email verification notification on registration', function (): void {
    Notification::fake();

    $this->post(route('auth.register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::query()->where('email', 'jane@example.com')->firstOrFail();

    Notification::assertSentTo($user, QueueableVerifyEmail::class);
});

it('requires a name', function (): void {
    $this->post(route('auth.register.store'), [
        'email' => 'jane@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('name');
});

it('requires a valid email', function (): void {
    $this->post(route('auth.register.store'), [
        'name' => 'Jane Doe',
        'email' => 'not-an-email',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('email');
});

it('requires a unique email', function (): void {
    User::factory()->create([
        'role_id' => Role::userRole()->id,
        'email' => 'existing@example.com',
    ]);

    $this->post(route('auth.register.store'), [
        'name' => 'Jane Doe',
        'email' => 'existing@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('email');
});

it('requires password confirmation', function (): void {
    $this->post(route('auth.register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'wrong',
    ])->assertSessionHasErrors('password');
});

it('requires a password of at least 8 characters', function (): void {
    $this->post(route('auth.register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'Ab1!',
        'password_confirmation' => 'Ab1!',
    ])->assertSessionHasErrors('password');
});
