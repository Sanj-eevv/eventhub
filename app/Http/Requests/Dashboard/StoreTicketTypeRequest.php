<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTicketTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'max_per_user' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'sale_starts_at' => ['nullable', 'date'],
            'sale_ends_at' => ['nullable', 'date', 'after_or_equal:sale_starts_at'],
        ];
    }

    public function toDto(): array
    {
        return [
            'name' => $this->validated('name'),
            'description' => $this->validated('description'),
            'price' => (int) round((float) $this->validated('price') * 100),
            'capacity' => (int) $this->validated('capacity'),
            'max_per_user' => $this->validated('max_per_user'),
            'sort_order' => $this->validated('sort_order'),
            'is_active' => $this->validated('is_active'),
            'sale_starts_at' => $this->validated('sale_starts_at'),
            'sale_ends_at' => $this->validated('sale_ends_at'),
        ];
    }
}
