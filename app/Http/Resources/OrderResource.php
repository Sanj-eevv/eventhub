<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use App\Models\Setting;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'currency' => $this->currency,
            'total' => $this->total,
            'refund_status' => $this->refund_status?->value,
            'reserved_at' => $this->reserved_at?->toISOString(),
            'expires_at' => $this->expires_at?->toISOString(),
            'paid_at' => $this->paid_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'refunded_at' => $this->refunded_at?->toISOString(),
            'can_download_pdf' => OrderStatus::Paid === $this->status,
            'can_cancel' => 'paid' === $this->status->value
                && $this->event->starts_at->isAfter(
                    CarbonImmutable::now()->addHours((int) Setting::get('cancellation_cutoff_hours', 24))
                ),
            'event' => EventResource::make($this->whenLoaded('event')),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
        ];
    }
}
