<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ticket_reservation_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'cancellation_cutoff_hours' => ['required', 'integer', 'min:0'],
            'refund_percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }
}
