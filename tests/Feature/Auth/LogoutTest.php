<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

it('logs out an authenticated user', function (): void {
    $user = User::factory()->create(['role_id' => Role::userRole()->id]);

    $this->actingAs($user)
        ->post(route('auth.logout'))
        ->assertRedirect();

    $this->assertGuest();
});

it('redirects guests attempting to logout', function (): void {
    $this->post(route('auth.logout'))->assertRedirect(route('auth.login'));
});
