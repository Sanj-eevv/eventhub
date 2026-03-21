<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\DataTransferObjects\EventData;
use App\DataTransferObjects\TicketTypeData;
use App\Models\Organization;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

final class EventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'organization_uuid' => ['required', 'string', 'exists:organizations,uuid'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:10000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'timezone' => ['required', 'string', 'timezone:all'],
            'location' => ['nullable', 'array'],
            'location.venue_name' => ['nullable', 'string', 'max:191'],
            'location.address_line_1' => ['nullable', 'string', 'max:191'],
            'location.address_line_2' => ['nullable', 'string', 'max:191'],
            'location.zip' => ['nullable', 'string', 'max:20'],
            'location.map_url' => ['nullable', 'url'],
            'ticket_types' => ['required', 'array', 'min:1'],
            'ticket_types.*.uuid' => ['nullable', 'string'],
            'ticket_types.*.name' => ['required', 'string', 'max:191'],
            'ticket_types.*.price' => ['required', 'numeric', 'min:0.01'],
            'ticket_types.*.capacity' => ['required', 'integer', 'min:1'],
            'ticket_types.*.max_per_user' => ['nullable', 'integer', 'min:1', 'max:100'],
            'ticket_types.*.sale_starts_at' => ['nullable', 'date'],
            'ticket_types.*.sale_ends_at' => ['nullable', 'date', 'after_or_equal:ticket_types.*.sale_starts_at'],
        ];
    }

    public function attributes(): array
    {
        return [
            'organization_uuid' => 'organization',
        ];
    }

    public function messages(): array
    {
        return [
            'location.map_url.url' => 'The map URL must be a valid URL.',
            'ticket_types.required' => 'At least one ticket type is required.',
            'ticket_types.min' => 'At least one ticket type is required.',
            'ticket_types.*.name.required' => 'Each ticket type must have a name.',
            'ticket_types.*.price.required' => 'Each ticket type must have a price.',
            'ticket_types.*.price.min' => 'Ticket price must be greater than zero.',
            'ticket_types.*.capacity.required' => 'Each ticket type must have a capacity.',
            'ticket_types.*.capacity.min' => 'Capacity must be at least 1.',
        ];
    }

    public function toDto(): EventData
    {
        $organizationId = Organization::where('uuid', $this->validated('organization_uuid'))->value('id');

        $ticketTypes = collect($this->validated('ticket_types'))
            ->map(fn (array $type, int $index) => new TicketTypeData(
                name: $type['name'],
                price: (int) round((float) $type['price'] * 100),
                capacity: (int) $type['capacity'],
                max_per_user: isset($type['max_per_user']) ? (int) $type['max_per_user'] : 5,
                sort_order: $index,
                uuid: $type['uuid'] ?? null,
                sale_starts_at: isset($type['sale_starts_at']) ? CarbonImmutable::parse($type['sale_starts_at']) : null,
                sale_ends_at: isset($type['sale_ends_at']) ? CarbonImmutable::parse($type['sale_ends_at']) : null,
            ))
            ->all();

        return new EventData(
            user_id: $this->user()->id,
            organization_id: $organizationId,
            title: $this->validated('title'),
            description: $this->validated('description'),
            starts_at: CarbonImmutable::parse($this->validated('starts_at')),
            ends_at: $this->validated('ends_at') ? CarbonImmutable::parse($this->validated('ends_at')) : null,
            timezone: $this->validated('timezone'),
            location: $this->validated('location'),
            ticket_types: $ticketTypes,
        );
    }
}
