<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\OrganizationData;
use App\Models\Organization;

final class UpdateOrganizationAction
{
    public function __invoke(Organization $organization, OrganizationData $data): Organization
    {
        $organization->update([
            'title' => $data->title,
            'description' => $data->description,
            'contact_address' => $data->contact_address,
            'contact_email' => $data->contact_email,
            'status' => $data->status,
        ]);

        return $organization;
    }
}
