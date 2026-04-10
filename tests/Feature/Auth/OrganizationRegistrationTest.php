<?php

declare(strict_types=1);

use App\Enums\OrganizationStatus;
use App\Models\Organization;
use App\Models\User;

it('renders the organization registration page for guests', function (): void {
    $this->get(route('auth.register.organization'))->assertSuccessful();
});

it('registers an organization and user with valid data', function (): void {
    $this->post(route('auth.register.organization.store'), [
        'title' => 'Acme Corp',
        'description' => 'We do everything.',
        'contact_address' => '123 Main St',
        'contact_email' => 'contact@acme.com',
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertRedirect();

    $this->assertDatabaseHas('organizations', ['contact_email' => 'contact@acme.com']);
    $this->assertDatabaseHas('users', ['email' => 'john@acme.com']);
    $this->assertAuthenticated();
});

it('creates the organization with Pending status', function (): void {
    $this->post(route('auth.register.organization.store'), [
        'title' => 'Acme Corp',
        'description' => 'We do everything.',
        'contact_address' => '123 Main St',
        'contact_email' => 'contact@acme.com',
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $organization = Organization::query()->where('contact_email', 'contact@acme.com')->firstOrFail();

    expect($organization->status)->toBe(OrganizationStatus::Pending);
});

it('assigns the OrganizationAdmin role to the registering user', function (): void {
    $this->post(route('auth.register.organization.store'), [
        'title' => 'Acme Corp',
        'description' => 'We do everything.',
        'contact_address' => '123 Main St',
        'contact_email' => 'contact@acme.com',
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::query()->where('email', 'john@acme.com')->firstOrFail();

    expect($user->role->slug)->toBe('organization-admin');
});

it('links the user to the new organization', function (): void {
    $this->post(route('auth.register.organization.store'), [
        'title' => 'Acme Corp',
        'description' => 'We do everything.',
        'contact_address' => '123 Main St',
        'contact_email' => 'contact@acme.com',
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $organization = Organization::query()->where('contact_email', 'contact@acme.com')->firstOrFail();
    $user = User::query()->where('email', 'john@acme.com')->firstOrFail();

    expect($user->organization_id)->toBe($organization->id);
});

it('requires an organization name', function (): void {
    $this->post(route('auth.register.organization.store'), [
        'description' => 'We do everything.',
        'contact_address' => '123 Main St',
        'contact_email' => 'contact@acme.com',
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('title');
});

it('requires a contact email', function (): void {
    $this->post(route('auth.register.organization.store'), [
        'title' => 'Acme Corp',
        'description' => 'We do everything.',
        'contact_address' => '123 Main St',
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('contact_email');
});

it('requires a unique organization contact email', function (): void {
    Organization::factory()->create(['contact_email' => 'taken@acme.com']);

    $this->post(route('auth.register.organization.store'), [
        'title' => 'Acme Corp',
        'description' => 'We do everything.',
        'contact_address' => '123 Main St',
        'contact_email' => 'taken@acme.com',
        'name' => 'John Admin',
        'email' => 'john@acme.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('contact_email');
});
