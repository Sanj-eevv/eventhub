<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Enums\OrganizationStatus;

final readonly class OrganizationData
{
    public function __construct(
        public string $title,
        public string $description,
        public string $contact_address,
        public string $contact_email,
        public OrganizationStatus $status,
    ) {}
}
