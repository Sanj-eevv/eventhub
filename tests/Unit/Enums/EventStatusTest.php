<?php

declare(strict_types=1);

use App\Enums\EventStatus;

it('allows Draft to transition to Published', function (): void {
    expect(EventStatus::Draft->canTransitionTo(EventStatus::Published))->toBeTrue();
});

it('does not allow Draft to transition to Cancelled', function (): void {
    expect(EventStatus::Draft->canTransitionTo(EventStatus::Cancelled))->toBeFalse();
});

it('does not allow Draft to transition to itself', function (): void {
    expect(EventStatus::Draft->canTransitionTo(EventStatus::Draft))->toBeFalse();
});

it('allows Published to transition to Draft', function (): void {
    expect(EventStatus::Published->canTransitionTo(EventStatus::Draft))->toBeTrue();
});

it('allows Published to transition to Cancelled', function (): void {
    expect(EventStatus::Published->canTransitionTo(EventStatus::Cancelled))->toBeTrue();
});

it('does not allow Published to transition to itself', function (): void {
    expect(EventStatus::Published->canTransitionTo(EventStatus::Published))->toBeFalse();
});

it('does not allow Cancelled to transition to any status', function (): void {
    foreach (EventStatus::cases() as $status) {
        expect(EventStatus::Cancelled->canTransitionTo($status))->toBeFalse();
    }
});

it('marks only Cancelled as final', function (): void {
    expect(EventStatus::Draft->isFinal())->toBeFalse()
        ->and(EventStatus::Published->isFinal())->toBeFalse()
        ->and(EventStatus::Cancelled->isFinal())->toBeTrue();
});

it('returns a non-empty label for every case', function (EventStatus $status): void {
    expect($status->label())->toBeString()->not->toBeEmpty();
})->with(EventStatus::cases());

it('returns a non-empty color for every case', function (EventStatus $status): void {
    expect($status->color())->toBeString()->not->toBeEmpty();
})->with(EventStatus::cases());
