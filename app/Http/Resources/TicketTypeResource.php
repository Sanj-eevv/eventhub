<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TicketType */
final class TicketTypeResource extends JsonResource
{
    /** @return array<string, mixed> */
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
            'effective_max_per_user' => $this->when(
                $request->user() && null !== $this->max_per_user,
                fn (): int => max(0, (int) $this->max_per_user - (int) ($this->user_tickets_count ?? 0))
            ),
            'sort_order' => $this->sort_order,
            'sale_starts_at' => $this->sale_starts_at?->toISOString(),
            'sale_ends_at' => $this->sale_ends_at?->toISOString(),
        ];
    }
}
