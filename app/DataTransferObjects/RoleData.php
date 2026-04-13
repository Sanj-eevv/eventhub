<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class RoleData
{
    public function __construct(
        public string $name,
        public string $description,
        /** @var string[]|null */
        public ?array $permissions = null,
    ) {}
}
