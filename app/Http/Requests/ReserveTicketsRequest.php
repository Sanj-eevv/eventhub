<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ReserveTicketsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.ticket_type_uuid' => ['required', 'string', 'exists:ticket_types,uuid'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
