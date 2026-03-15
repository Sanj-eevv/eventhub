<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class RoleDto
{
    public function __construct(
        public string $name,
        public string $description,
        public ?array $permissions = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
            permissions: $data['permissions'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'permissions' => $this->permissions,
        ];
    }
}
