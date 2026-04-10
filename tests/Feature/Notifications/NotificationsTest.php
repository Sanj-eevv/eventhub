<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class);

it('redirects guests to login', function (): void {
    $this->get(route('notifications.index'))->assertRedirect(route('auth.login'));
});

it('shows the notifications page to authenticated users', function (): void {
    $user = $this->createUser();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertSuccessful();
});

it('marks a single notification as read', function (): void {
    $user = $this->createUser();

    $notification = $user->notifications()->create([
        'id' => Str::uuid(),
        'type' => 'App\Notifications\OrderConfirmedNotification',
        'data' => ['title' => 'Test'],
        'read_at' => null,
    ]);

    $this->actingAs($user)
        ->patch(route('notifications.read', ['notificationId' => $notification->id]))
        ->assertRedirect();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('marks all notifications as read', function (): void {
    $user = $this->createUser();

    $user->notifications()->createMany([
        ['id' => Str::uuid(), 'type' => 'test', 'data' => ['title' => 'A'], 'read_at' => null],
        ['id' => Str::uuid(), 'type' => 'test', 'data' => ['title' => 'B'], 'read_at' => null],
    ]);

    $this->actingAs($user)
        ->delete(route('notifications.read-all'))
        ->assertRedirect();

    expect($user->notifications()->whereNull('read_at')->count())->toBe(0);
});
