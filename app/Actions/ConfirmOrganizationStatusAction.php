<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrganizationStatus;
use App\Events\OrganizationApproved;
use App\Events\OrganizationRejected;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Organization;
use Illuminate\Events\Dispatcher;

final class ConfirmOrganizationStatusAction
{
    public function __construct(private readonly Dispatcher $dispatcher) {}

    public function execute(Organization $organization, OrganizationStatus $status, bool $notify = true): void
    {
        if ( ! $organization->status->canTransitionTo($status)) {
            throw new InvalidStatusTransitionException($organization->status, $status);
        }

        $organization->update(['status' => $status]);

        $event = OrganizationStatus::Approved === $status
            ? new OrganizationApproved($organization, $notify)
            : new OrganizationRejected($organization, $notify);

        $this->dispatcher->dispatch($event);
    }
}
