<?php

declare(strict_types=1);

use App\Enums\OrganizationStatus;

it('allows Pending to transition to Approved', function (): void {
    expect(OrganizationStatus::Pending->canTransitionTo(OrganizationStatus::Approved))->toBeTrue();
});

it('allows Pending to transition to Rejected', function (): void {
    expect(OrganizationStatus::Pending->canTransitionTo(OrganizationStatus::Rejected))->toBeTrue();
});

it('does not allow Pending to transition to Suspended', function (): void {
    expect(OrganizationStatus::Pending->canTransitionTo(OrganizationStatus::Suspended))->toBeFalse();
});

it('allows Approved to transition to Suspended', function (): void {
    expect(OrganizationStatus::Approved->canTransitionTo(OrganizationStatus::Suspended))->toBeTrue();
});

it('does not allow Approved to transition to Pending or Rejected', function (): void {
    expect(OrganizationStatus::Approved->canTransitionTo(OrganizationStatus::Pending))->toBeFalse()
        ->and(OrganizationStatus::Approved->canTransitionTo(OrganizationStatus::Rejected))->toBeFalse();
});

it('does not allow Rejected to transition to any status', function (): void {
    foreach (OrganizationStatus::cases() as $status) {
        expect(OrganizationStatus::Rejected->canTransitionTo($status))->toBeFalse();
    }
});

it('does not allow Suspended to transition to any status', function (): void {
    foreach (OrganizationStatus::cases() as $status) {
        expect(OrganizationStatus::Suspended->canTransitionTo($status))->toBeFalse();
    }
});

it('marks Rejected and Suspended as final', function (): void {
    expect(OrganizationStatus::Pending->isFinal())->toBeFalse()
        ->and(OrganizationStatus::Approved->isFinal())->toBeFalse()
        ->and(OrganizationStatus::Rejected->isFinal())->toBeTrue()
        ->and(OrganizationStatus::Suspended->isFinal())->toBeTrue();
});

it('returns a non-empty label for every case', function (OrganizationStatus $status): void {
    expect($status->label())->toBeString()->not->toBeEmpty();
})->with(OrganizationStatus::cases());

it('returns a non-empty color for every case', function (OrganizationStatus $status): void {
    expect($status->color())->toBeString()->not->toBeEmpty();
})->with(OrganizationStatus::cases());
