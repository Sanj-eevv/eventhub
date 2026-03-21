<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTicketTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'max_per_user' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'sale_starts_at' => ['nullable', 'date'],
            'sale_ends_at' => ['nullable', 'date', 'after_or_equal:sale_starts_at'],
        ];
    }

    public function toDto(): array
    {
        $data = array_filter($this->validated(), fn (mixed $value) => null !== $value);

        if (isset($data['price'])) {
            $data['price'] = (int) round((float) $data['price'] * 100);
        }

        return $data;
    }
}
