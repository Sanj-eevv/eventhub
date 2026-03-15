<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public int $role_id,
        public ?int $organization_id = null,
    ) {}

    public function withOrganizationId(int $organizationId): self
    {
        return new self(
            name: $this->name,
            email: $this->email,
            password: $this->password,
            role_id: $this->role_id,
            organization_id: $organizationId,
        );
    }
}
