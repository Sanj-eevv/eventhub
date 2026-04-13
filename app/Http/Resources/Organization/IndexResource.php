<?php

declare(strict_types=1);

namespace App\Http\Resources\Organization;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Organization */
final class IndexResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'contact_email' => $this->contact_email,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label(), 'color' => $this->status->color()],
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
