<?php

declare(strict_types=1);

use App\Models\Setting;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class);

it('allows super admins to view the settings form', function (): void {
    $this->actingAs($this->createSuperAdmin())
        ->get(route('dashboard.settings.edit'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->has('settings'));
});

it('forbids users without setting permission from viewing settings', function (): void {
    $this->actingAs($this->createAdmin())
        ->get(route('dashboard.settings.edit'))
        ->assertForbidden();
});

it('allows super admins to update settings', function (): void {
    $this->actingAs($this->createSuperAdmin())
        ->put(route('dashboard.settings.update'), [
            'ticket_reservation_minutes' => 20,
            'cancellation_cutoff_hours' => 48,
            'refund_percentage' => 80,
        ])
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect(Setting::query()->where('key', 'ticket_reservation_minutes')->value('value'))->toBe('20');
    expect(Setting::query()->where('key', 'cancellation_cutoff_hours')->value('value'))->toBe('48');
    expect(Setting::query()->where('key', 'refund_percentage')->value('value'))->toBe('80');
});

it('validates that refund percentage is between 0 and 100', function (): void {
    $this->actingAs($this->createSuperAdmin())
        ->put(route('dashboard.settings.update'), [
            'ticket_reservation_minutes' => 15,
            'cancellation_cutoff_hours' => 24,
            'refund_percentage' => 150,
        ])
        ->assertSessionHasErrors('refund_percentage');
});

it('validates that ticket reservation minutes is a positive integer', function (): void {
    $this->actingAs($this->createSuperAdmin())
        ->put(route('dashboard.settings.update'), [
            'ticket_reservation_minutes' => -5,
            'cancellation_cutoff_hours' => 24,
            'refund_percentage' => 100,
        ])
        ->assertSessionHasErrors('ticket_reservation_minutes');
});
