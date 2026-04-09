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
use Illuminate\Events\Dispatcher;

final class ConfirmOrganizationStatusAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly RecordActivityAction $recordActivityAction,
    ) {}

    public function execute(Organization $organization, OrganizationStatus $status, bool $notify = true, ?User $causer = null): void
    {
        if ( ! $organization->status->canTransitionTo($status)) {
            throw new InvalidStatusTransitionException($organization->status, $status);
        }

        $organization->update(['status' => $status]);

        $event = OrganizationStatus::Approved === $status
            ? new OrganizationApproved($organization, $notify)
            : new OrganizationRejected($organization, $notify);

        $this->dispatcher->dispatch($event);

        $activityEvent = OrganizationStatus::Approved === $status
            ? ActivityEvent::OrganizationApproved
            : ActivityEvent::OrganizationRejected;

        $this->recordActivityAction->execute($activityEvent, $organization, $causer);
    }
}
