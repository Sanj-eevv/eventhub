<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;

final readonly class EventDto
{
    public function __construct(
        public int $user_id,
        public int $organization_id,
        public string $title,
        public string $description,
        public CarbonImmutable $starts_at,
        public ?CarbonImmutable $ends_at,
        public string $timezone,
        public ?array $location,
        public ?array $tickets,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user_id: $data['user_id'],
            organization_id: $data['organization_id'],
            title: $data['title'],
            description: $data['description'],
            starts_at: CarbonImmutable::parse($data['starts_at']),
            ends_at: isset($data['ends_at']) ? CarbonImmutable::parse($data['ends_at']) : null,
            timezone: $data['timezone'],
            location: $data['location'] ?? null,
            tickets: $data['tickets'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'organization_id' => $this->organization_id,
            'title' => $this->title,
            'description' => $this->description,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'timezone' => $this->timezone,
            'location' => $this->location,
            'tickets' => $this->tickets,
        ];
    }
}
