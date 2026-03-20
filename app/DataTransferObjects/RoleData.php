<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class RoleData
{
    public function __construct(
        public string $name,
        public string $description,
        public ?array $permissions = null,
    ) {}
}
