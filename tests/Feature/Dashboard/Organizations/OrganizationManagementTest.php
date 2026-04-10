<?php

declare(strict_types=1);

use App\Enums\OrganizationStatus;
use App\Models\Organization;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class);

it('allows admins to list organizations', function (): void {
    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.organizations.index'))
        ->assertSuccessful();
});

it('forbids users without permission from listing organizations', function (): void {
    $this->actingAs($this->createUser())
        ->get(route('dashboard.organizations.index'))
        ->assertForbidden();
});

it('allows admins to view an organization', function (): void {
    $organization = Organization::factory()->create();

    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.organizations.show', $organization))
        ->assertSuccessful();
});

it('allows admins to create an organization', function (): void {
    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.organizations.store'), [
            'title' => 'New Org',
            'description' => 'A description here.',
            'contact_address' => '123 Main St',
            'contact_email' => 'neworg@example.com',
            'status' => OrganizationStatus::Pending->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    $this->assertDatabaseHas('organizations', ['contact_email' => 'neworg@example.com']);
});

it('validates required fields when creating an organization', function (): void {
    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.organizations.store'), [])
        ->assertSessionHasErrors(['title', 'description', 'contact_address', 'contact_email', 'status']);
});

it('validates unique contact email when creating an organization', function (): void {
    Organization::factory()->create(['contact_email' => 'taken@example.com']);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.organizations.store'), [
            'title' => 'Org',
            'description' => 'Desc',
            'contact_address' => 'Addr',
            'contact_email' => 'taken@example.com',
            'status' => OrganizationStatus::Pending->value,
        ])
        ->assertSessionHasErrors('contact_email');
});

it('allows admins to update an organization', function (): void {
    $organization = Organization::factory()->create();

    $this->actingAs($this->createAdmin())
        ->put(route('dashboard.organizations.update', $organization), [
            'title' => 'Updated Title',
            'description' => 'Updated desc.',
            'contact_address' => '456 New Ave',
            'contact_email' => 'updated@example.com',
            'status' => OrganizationStatus::Approved->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($organization->fresh()->title)->toBe('Updated Title');
});

it('allows admins to delete an organization', function (): void {
    $organization = Organization::factory()->create();

    $this->actingAs($this->createAdmin())
        ->delete(route('dashboard.organizations.destroy', $organization))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    $this->assertSoftDeleted('organizations', ['id' => $organization->id]);
});

it('allows admins to approve a pending organization', function (): void {
    $organization = Organization::factory()->create(['status' => OrganizationStatus::Pending]);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.organizations.approve', $organization))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($organization->fresh()->status)->toBe(OrganizationStatus::Approved);
});

it('blocks approving an already-approved organization', function (): void {
    $organization = Organization::factory()->approved()->create();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.organizations.approve', $organization))
        ->assertRedirect()
        ->assertSessionHas('toast_error');
});

it('allows admins to reject a pending organization', function (): void {
    $organization = Organization::factory()->create(['status' => OrganizationStatus::Pending]);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.organizations.reject', $organization))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($organization->fresh()->status)->toBe(OrganizationStatus::Rejected);
});

it('blocks rejecting an already-rejected organization', function (): void {
    $organization = Organization::factory()->create(['status' => OrganizationStatus::Rejected]);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.organizations.reject', $organization))
        ->assertRedirect()
        ->assertSessionHas('toast_error');
});

it('forbids organization admins from approving organizations', function (): void {
    $organization = Organization::factory()->create(['status' => OrganizationStatus::Pending]);

    $this->actingAs($this->createOrganizationAdmin())
        ->post(route('dashboard.organizations.approve', $organization))
        ->assertForbidden();
});
