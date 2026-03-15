<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\OrganizationData;
use App\DataTransferObjects\UserData;
use App\Enums\OrganizationStatus;
use App\Mail\Organizations\StatusApproved;
use App\Mail\Organizations\StatusRejected;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class OrganizationService
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function create(OrganizationData $data): Organization
    {
        return Organization::query()->create($data->toArray());
    }

    public function createWithAdmin(OrganizationData $orgData, UserData $userData): User
    {
        return DB::transaction(fn (): User => $this->userService->create(new UserData(
            name: $userData->name,
            email: $userData->email,
            password: $userData->password,
            role_id: $userData->role_id,
            organization_id: $this->create($orgData)->id,
        )));
    }

    public function update(Organization $organization, OrganizationData $data): bool
    {
        return $organization->update($data->toArray());
    }

    public function confirmStatus(Organization $organization, OrganizationStatus $status, bool $silent = false): void
    {
        $organization->update([
            'status' => $status,
            'verified_at' => OrganizationStatus::Approved === $status ? now() : null,
        ]);

        if ( ! $silent) {
            $mailable = OrganizationStatus::Approved === $status
                ? new StatusApproved($organization)
                : new StatusRejected($organization);

            Mail::to($organization->contact_email)->queue($mailable);
        }
    }

    public function delete(Organization $organization): void
    {
        $organization->delete();
        $organization->users()->delete();
        $organization->delete();
    }
}
