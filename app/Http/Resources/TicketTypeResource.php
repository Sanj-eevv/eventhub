<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\DateFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TicketTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $timezone = $this->event->timezone;

        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price / 100,
            'capacity' => $this->capacity,
            'max_per_user' => $this->max_per_user,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'sale_starts_at' => $this->sale_starts_at?->setTimezone($timezone)->format(DateFormat::DATETIME_LOCAL),
            'sale_ends_at' => $this->sale_ends_at?->setTimezone($timezone)->format(DateFormat::DATETIME_LOCAL),
        ];
    }
}
