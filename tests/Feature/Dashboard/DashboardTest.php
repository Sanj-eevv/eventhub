<?php

declare(strict_types=1);

use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class);

it('redirects guests to login', function (): void {
    $this->get(route('dashboard.index'))->assertRedirect(route('auth.login'));
});

it('forbids regular users without dashboard permission', function (): void {
    $this->actingAs($this->createUser())
        ->get(route('dashboard.index'))
        ->assertForbidden();
});

it('allows admins to access the dashboard', function (): void {
    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.index'))
        ->assertSuccessful();
});

it('allows super admins to access the dashboard', function (): void {
    $this->actingAs($this->createSuperAdmin())
        ->get(route('dashboard.index'))
        ->assertSuccessful();
});

it('allows organization admins to access the dashboard', function (): void {
    $this->actingAs($this->createOrganizationAdmin())
        ->get(route('dashboard.index'))
        ->assertSuccessful();
});
