<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TicketTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'capacity' => $this->capacity,
            'available_capacity' => max(0, $this->capacity - ($this->tickets_count ?? 0)),
            'max_per_user' => $this->max_per_user,
            'sort_order' => $this->sort_order,
            'sale_starts_at' => $this->sale_starts_at?->toISOString(),
            'sale_ends_at' => $this->sale_ends_at?->toISOString(),
        ];
    }
}
