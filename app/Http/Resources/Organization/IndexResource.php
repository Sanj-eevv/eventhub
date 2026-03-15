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
            'status' => $this->status,
            'created_at' => formatUserTime($this->created_at, 'd M Y'),
        ];
    }
}
