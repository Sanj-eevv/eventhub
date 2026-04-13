<?php

declare(strict_types=1);

namespace App\Http\Resources\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
final class IndexResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label(), 'color' => $this->status->color()],
            'total' => $this->total,
            'currency' => $this->currency,
            'paid_at' => $this->paid_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'event' => $this->whenLoaded('event', fn (): array => ['title' => $this->event->title]),
            'user' => $this->whenLoaded('user', fn (): array => ['name' => $this->user->name, 'email' => $this->user->email]),
        ];
    }
}
