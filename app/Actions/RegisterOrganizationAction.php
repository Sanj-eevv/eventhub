<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\OrganizationData;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\DatabaseManager;

final class RegisterOrganizationAction
{
    public function __construct(
        private readonly CreateUserAction $createUserAction,
        private readonly DatabaseManager $databaseManager,
    ) {}

    public function execute(OrganizationData $organizationData): User
    {
        return $this->databaseManager->transaction(function () use ($organizationData): User {
            $organization = Organization::query()->create([
                'title' => $organizationData->title,
                'description' => $organizationData->description,
                'contact_address' => $organizationData->contact_address,
                'contact_email' => $organizationData->contact_email,
                'status' => $organizationData->status->value,
            ]);

            return $this->createUserAction->execute($organizationData->user->withOrganizationId($organization->id));
        });
    }
}
