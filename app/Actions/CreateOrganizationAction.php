<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\OrganizationData;
use App\Models\Organization;

final class CreateOrganizationAction
{
    public function __construct() {}

    public function execute(OrganizationData $organizationData): Organization
    {
        return Organization::query()->create([
            'title' => $organizationData->title,
            'description' => $organizationData->description,
            'contact_address' => $organizationData->contact_address,
            'contact_email' => $organizationData->contact_email,
            'status' => $organizationData->status->value,
        ]);
    }
}
