<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DataTransferObjects\SettingsData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin SettingsData */
final class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ticket_reservation_minutes' => $this->ticketReservationMinutes,
            'cancellation_cutoff_hours' => $this->cancellationCutoffHours,
            'refund_percentage' => $this->refundPercentage->value,
        ];
    }
}
