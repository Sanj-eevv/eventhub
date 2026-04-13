<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Enums\OrganizationStatus;
use App\Events\OrganizationApproved;
use App\Events\OrganizationRejected;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class ConfirmOrganizationStatusAction
{
    public function __construct(
        private Dispatcher $dispatcher,
        private RecordActivityAction $recordActivityAction,
    ) {}

    public function __invoke(Organization $organization, OrganizationStatus $status, bool $notify = true, ?User $causer = null): void
    {
        if ( ! $organization->status->canTransitionTo($status)) {
            throw new InvalidStatusTransitionException($organization->status, $status);
        }

        $organization->update(['status' => $status]);

        [$broadcastEvent, $activityEvent] = match ($status) {
            OrganizationStatus::Approved => [
                new OrganizationApproved($organization, $notify),
                ActivityEvent::OrganizationApproved,
            ],
            OrganizationStatus::Rejected => [
                new OrganizationRejected($organization, $notify),
                ActivityEvent::OrganizationRejected,
            ],
        };

        $this->dispatcher->dispatch($broadcastEvent);
        ($this->recordActivityAction)($activityEvent, $organization, $causer);
    }
}
