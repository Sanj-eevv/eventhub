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
            'items.*.ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}
