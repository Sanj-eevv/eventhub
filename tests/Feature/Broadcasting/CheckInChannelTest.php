<?php

declare(strict_types=1);

use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class);

it('allows an admin to subscribe to the check-in channel', function (): void {
    $event = $this->createPublishedEvent();
    $admin = $this->createAdmin();

    expect($admin->can('checkIn', $event))->toBeTrue();
});

it('allows an organization admin of the event to subscribe to the check-in channel', function (): void {
    [$event] = $this->createPublishedEventWithTicketType();
    $orgAdmin = $this->createOrganizationAdmin($event->organization);

    expect($orgAdmin->can('checkIn', $event))->toBeTrue();
});

it('denies a regular user from subscribing to the check-in channel', function (): void {
    $event = $this->createPublishedEvent();

    expect($this->createUser()->can('checkIn', $event))->toBeFalse();
});
