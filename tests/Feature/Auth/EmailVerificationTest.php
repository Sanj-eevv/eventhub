<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Notifications\QueueableVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

it('renders the verification notice for authenticated unverified users', function (): void {
    $user = User::factory()->unverified()->create(['role_id' => Role::userRole()->id]);

    $this->actingAs($user)
        ->get(route('auth.verification.notice'))
        ->assertSuccessful();
});

it('redirects unverified users to the verification notice on protected routes', function (): void {
    $user = User::factory()->unverified()->create(['role_id' => Role::userRole()->id]);

    $this->actingAs($user)
        ->get(route('orders.index'))
        ->assertRedirect(route('auth.verification.notice'));
});

it('verifies the user via a valid signed URL', function (): void {
    Event::fake([Verified::class]);

    $user = User::factory()->unverified()->create(['role_id' => Role::userRole()->id]);

    $verificationUrl = URL::temporarySignedRoute(
        'auth.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $this->actingAs($user)->get($verificationUrl)->assertRedirect();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Event::assertDispatched(Verified::class);
});

it('returns 403 for an invalid signed verification URL', function (): void {
    $user = User::factory()->unverified()->create(['role_id' => Role::userRole()->id]);

    $this->actingAs($user)
        ->get(route('auth.verification.verify', ['id' => $user->id, 'hash' => 'invalid-hash']))
        ->assertForbidden();
});

it('resends the verification notification', function (): void {
    Notification::fake();

    $user = User::factory()->unverified()->create(['role_id' => Role::userRole()->id]);

    $this->actingAs($user)
        ->post(route('auth.verification.send'))
        ->assertRedirect();

    Notification::assertSentTo($user, QueueableVerifyEmail::class);
});

it('redirects already verified users clicking a verification link', function (): void {
    $user = User::factory()->create(['role_id' => Role::userRole()->id]);

    $verificationUrl = URL::temporarySignedRoute(
        'auth.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $this->actingAs($user)->get($verificationUrl)->assertRedirect();
});
