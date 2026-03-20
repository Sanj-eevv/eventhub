<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\DataTransferObjects\EventData;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

final class EventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:10000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'timezone' => ['required', 'string', 'timezone:all'],
            'location' => ['nullable', 'array'],
            'location.venue_name' => ['nullable', 'string', 'max:191'],
            'location.address_line_1' => ['nullable', 'string', 'max:191'],
            'location.address_line_2' => ['nullable', 'string', 'max:191'],
            'location.city' => ['nullable', 'string', 'max:191'],
            'location.state' => ['nullable', 'string', 'max:191'],
            'location.zip' => ['nullable', 'string', 'max:20'],
            'location.country' => ['nullable', 'string', 'max:191'],
            'location.map_url' => ['nullable', 'url'],
            'tickets' => ['nullable', 'array'],
            'tickets.*.label' => ['required_with:tickets', 'string', 'max:191'],
            'tickets.*.price' => ['nullable', 'numeric', 'min:0'],
            'tickets.*.quantity' => ['nullable', 'integer', 'min:1'],
            'tickets.*.sale_starts_at' => ['nullable', 'date'],
            'tickets.*.sale_ends_at' => ['nullable', 'date', 'after:tickets.*.sale_starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'location.map_url.url' => 'The map URL must be a valid URL.',
            'tickets.*.label.required_with' => 'Each ticket must have a label.',
            'tickets.*.label.max' => 'Ticket labels may not exceed 191 characters.',
            'tickets.*.price.numeric' => 'Ticket price must be a number.',
            'tickets.*.price.min' => 'Ticket price must be at least 0.',
            'tickets.*.quantity.integer' => 'Ticket quantity must be a whole number.',
            'tickets.*.quantity.min' => 'Ticket quantity must be at least 1.',
            'tickets.*.sale_starts_at.date' => 'Ticket sale start date must be a valid date.',
            'tickets.*.sale_ends_at.date' => 'Ticket sale end date must be a valid date.',
            'tickets.*.sale_ends_at.after' => 'Ticket sale end date must be after the sale start date.',
        ];
    }

    public function toDto(): EventData
    {
        return new EventData(
            user_id: $this->user()->id,
            organization_id: $this->validated('organization_id'),
            title: $this->validated('title'),
            description: $this->validated('description'),
            starts_at: CarbonImmutable::parse($this->validated('starts_at')),
            ends_at: $this->validated('ends_at') ? CarbonImmutable::parse($this->validated('ends_at')) : null,
            timezone: $this->validated('timezone'),
            location: $this->validated('location'),
            tickets: $this->validated('tickets'),
        );
    }
}
