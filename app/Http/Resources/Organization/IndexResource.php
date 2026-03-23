<?php

declare(strict_types=1);

namespace App\Http\Resources\Organization;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class IndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'contact_email' => $this->contact_email,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
