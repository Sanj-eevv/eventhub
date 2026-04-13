<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\OrganizationData;
use App\DataTransferObjects\UserData;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\DatabaseManager;

final readonly class RegisterOrganizationAction
{
    public function __construct(
        private CreateUserAction $createUserAction,
        private DatabaseManager $databaseManager,
    ) {}

    public function __invoke(OrganizationData $organizationData, UserData $userData): User
    {
        return $this->databaseManager->transaction(function () use ($organizationData, $userData): User {
            $organization = Organization::query()->create([
                'title' => $organizationData->title,
                'description' => $organizationData->description,
                'contact_address' => $organizationData->contact_address,
                'contact_email' => $organizationData->contact_email,
                'status' => $organizationData->status->value,
            ]);

            return ($this->createUserAction)($userData->withOrganizationUuid($organization->uuid));
        });
    }
}
