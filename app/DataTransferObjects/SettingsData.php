<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\Setting;
use App\ValueObjects\Percentage;

final readonly class SettingsData
{
    public function __construct(
        public int $ticketReservationMinutes = 15,
        public int $cancellationCutoffHours = 24,
        public Percentage $refundPercentage = new Percentage(100),
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            ticketReservationMinutes: (int) ($data['ticket_reservation_minutes'] ?? 15),
            cancellationCutoffHours: (int) ($data['cancellation_cutoff_hours'] ?? 24),
            refundPercentage: Percentage::fromInt((int) ($data['refund_percentage'] ?? 100)),
        );
    }

    public static function fromDatabase(): self
    {
        $rows = Setting::query()->pluck('value', 'key');

        return new self(
            ticketReservationMinutes: (int) ($rows['ticket_reservation_minutes'] ?? 15),
            cancellationCutoffHours: (int) ($rows['cancellation_cutoff_hours'] ?? 24),
            refundPercentage: Percentage::fromInt((int) ($rows['refund_percentage'] ?? 100)),
        );
    }

    public function toArray(): array
    {
        return [
            'ticket_reservation_minutes' => $this->ticketReservationMinutes,
            'cancellation_cutoff_hours' => $this->cancellationCutoffHours,
            'refund_percentage' => $this->refundPercentage->value,
        ];
    }
}
