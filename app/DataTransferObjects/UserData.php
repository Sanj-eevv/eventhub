<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public string $role_slug,
        public ?string $organization_uuid = null,
    ) {}

    public function withOrganizationUuid(string $organizationUuid): self
    {
        return new self(
            name: $this->name,
            email: $this->email,
            password: $this->password,
            role_slug: $this->role_slug,
            organization_uuid: $organizationUuid,
        );
    }
}
