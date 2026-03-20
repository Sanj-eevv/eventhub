<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrganizationStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Mail\Organizations\StatusApproved;
use App\Mail\Organizations\StatusRejected;
use App\Models\Organization;
use Illuminate\Support\Facades\Mail;

final class ConfirmOrganizationStatusAction
{
    public function execute(Organization $organization, OrganizationStatus $status, bool $silent = false): void
    {
        if ( ! $organization->status->canTransitionTo($status)) {
            throw new InvalidStatusTransitionException($organization->status, $status);
        }

        $organization->update([
            'status' => $status,
        ]);

        if ( ! $silent) {
            $mailable = OrganizationStatus::Approved === $status
                ? new StatusApproved($organization)
                : new StatusRejected($organization);

            Mail::to($organization->contact_email)->queue($mailable);
        }
    }
}
