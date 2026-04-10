<?php

declare(strict_types=1);

use App\Enums\OrderStatus;

it('allows Reserved to transition to Paid', function (): void {
    expect(OrderStatus::Reserved->canTransitionTo(OrderStatus::Paid))->toBeTrue();
});

it('allows Reserved to transition to Cancelled', function (): void {
    expect(OrderStatus::Reserved->canTransitionTo(OrderStatus::Cancelled))->toBeTrue();
});

it('does not allow Reserved to transition to itself', function (): void {
    expect(OrderStatus::Reserved->canTransitionTo(OrderStatus::Reserved))->toBeFalse();
});

it('allows Paid to transition to Cancelled', function (): void {
    expect(OrderStatus::Paid->canTransitionTo(OrderStatus::Cancelled))->toBeTrue();
});

it('does not allow Paid to transition to Reserved', function (): void {
    expect(OrderStatus::Paid->canTransitionTo(OrderStatus::Reserved))->toBeFalse();
});

it('does not allow Cancelled to transition to any status', function (): void {
    foreach (OrderStatus::cases() as $status) {
        expect(OrderStatus::Cancelled->canTransitionTo($status))->toBeFalse();
    }
});

it('marks only Cancelled as final', function (): void {
    expect(OrderStatus::Reserved->isFinal())->toBeFalse()
        ->and(OrderStatus::Paid->isFinal())->toBeFalse()
        ->and(OrderStatus::Cancelled->isFinal())->toBeTrue();
});

it('returns a non-empty label for every case', function (OrderStatus $status): void {
    expect($status->label())->toBeString()->not->toBeEmpty();
})->with(OrderStatus::cases());

it('returns a non-empty color for every case', function (OrderStatus $status): void {
    expect($status->color())->toBeString()->not->toBeEmpty();
})->with(OrderStatus::cases());
