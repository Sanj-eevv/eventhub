<?php

declare(strict_types=1);

use App\Enums\TicketStatus;

it('allows Pending to transition to Active', function (): void {
    expect(TicketStatus::Pending->canTransitionTo(TicketStatus::Active))->toBeTrue();
});

it('allows Pending to transition to Cancelled', function (): void {
    expect(TicketStatus::Pending->canTransitionTo(TicketStatus::Cancelled))->toBeTrue();
});

it('does not allow Pending to transition to Used', function (): void {
    expect(TicketStatus::Pending->canTransitionTo(TicketStatus::Used))->toBeFalse();
});

it('allows Active to transition to Used', function (): void {
    expect(TicketStatus::Active->canTransitionTo(TicketStatus::Used))->toBeTrue();
});

it('allows Active to transition to Cancelled', function (): void {
    expect(TicketStatus::Active->canTransitionTo(TicketStatus::Cancelled))->toBeTrue();
});

it('does not allow Active to transition to Pending', function (): void {
    expect(TicketStatus::Active->canTransitionTo(TicketStatus::Pending))->toBeFalse();
});

it('does not allow Used to transition to any status', function (): void {
    foreach (TicketStatus::cases() as $status) {
        expect(TicketStatus::Used->canTransitionTo($status))->toBeFalse();
    }
});

it('does not allow Cancelled to transition to any status', function (): void {
    foreach (TicketStatus::cases() as $status) {
        expect(TicketStatus::Cancelled->canTransitionTo($status))->toBeFalse();
    }
});

it('marks Used and Cancelled as final', function (): void {
    expect(TicketStatus::Pending->isFinal())->toBeFalse()
        ->and(TicketStatus::Active->isFinal())->toBeFalse()
        ->and(TicketStatus::Used->isFinal())->toBeTrue()
        ->and(TicketStatus::Cancelled->isFinal())->toBeTrue();
});

it('returns a non-empty label for every case', function (TicketStatus $status): void {
    expect($status->label())->toBeString()->not->toBeEmpty();
})->with(TicketStatus::cases());

it('returns a non-empty color for every case', function (TicketStatus $status): void {
    expect($status->color())->toBeString()->not->toBeEmpty();
})->with(TicketStatus::cases());
