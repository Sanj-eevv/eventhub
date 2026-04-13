<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\DataTransferObjects\EventData;
use App\DataTransferObjects\TicketTypeData;
use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\Organization;
use App\Rules\EndsOnDifferentCalendarDay;
use App\Support\DateFormat;
use App\ValueObjects\DateRange;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class EventRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $dateFormat = 'date_format:'.DateFormat::DATETIME_LOCAL;

        return [
            'organization_uuid' => ['required', 'string', 'exists:organizations,uuid'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:10000'],
            'starts_at' => ['required', $dateFormat],
            'ends_at' => ['required', $dateFormat, 'after:starts_at', new EndsOnDifferentCalendarDay()],
            'timezone' => ['required', 'string', 'timezone:all'],
            'venue_name' => ['required', 'string', 'max:191'],
            'address' => ['required', 'string', 'max:191'],
            'zip' => ['required', 'string', 'max:20'],
            'map_url' => ['nullable', 'url'],
            'ticket_types' => ['required', 'array', 'min:1'],
            'ticket_types.*.uuid' => ['nullable', 'string'],
            'ticket_types.*.name' => ['required', 'string', 'max:191'],
            'ticket_types.*.description' => ['required', 'string', 'max:500'],
            'ticket_types.*.price' => ['required', 'numeric', 'min:1', 'regex:/^\d*(\.\d{1,2})?$/'],
            'ticket_types.*.capacity' => ['required', 'integer', 'min:1'],
            'ticket_types.*.max_per_user' => ['nullable', 'integer', 'min:1', 'max:100'],
            'ticket_types.*.sale_starts_at' => ['nullable', $dateFormat, 'before:starts_at'],
            'ticket_types.*.sale_ends_at' => ['nullable', $dateFormat, 'before:starts_at', 'after_or_equal:ticket_types.*.sale_starts_at'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('ticket_types', []) as $index => $ticketType) {
                if (filled($ticketType['sale_ends_at'] ?? null) && blank($ticketType['sale_starts_at'] ?? null)) {
                    $validator->errors()->add(
                        sprintf('ticket_types.%s.sale_ends_at', $index),
                        'A sale end date requires a sale start date.'
                    );
                }
            }

            $event = $this->route('event');

            if ( ! $event instanceof Event) {
                return;
            }

            $submittedUuids = collect($this->input('ticket_types', []))->pluck('uuid');

            $event->ticketTypes()
                ->whereNotIn('uuid', $submittedUuids)
                ->whereHas('tickets', fn ($query) => $query->where('status', TicketStatus::Active))
                ->each(function (TicketType $ticketType) use ($validator): void {
                    $validator->errors()->add(
                        'ticket_types',
                        sprintf('The "%s" ticket type has paid tickets and cannot be removed.', $ticketType->name)
                    );
                });
        });
    }

    public function attributes(): array
    {
        return [
            'organization_uuid' => 'organization',
            'ticket_types.*.description' => 'description',
            'ticket_types.*.sale_starts_at' => 'sale start date',
            'ticket_types.*.sale_ends_at' => 'sale end date',
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
        $timezone = $this->validated('timezone');
        $organizationId = Organization::query()->where('uuid', $this->validated('organization_uuid'))->value('id');
        $ticketTypes = collect($this->validated('ticket_types'))
            ->map(fn (array $type, int $index): TicketTypeData => new TicketTypeData(
                name: $type['name'],
                description: $type['description'],
                price: (int) round($type['price'] * 100),
                capacity: (int) $type['capacity'],
                sort_order: $index,
                uuid: $type['uuid'] ?? '',
                max_per_user: isset($type['max_per_user']) ? (int) $type['max_per_user'] : null,
                sale_starts_at: isset($type['sale_starts_at']) ? CarbonImmutable::parse($type['sale_starts_at'], $timezone)->utc() : null,
                sale_ends_at: isset($type['sale_ends_at']) ? CarbonImmutable::parse($type['sale_ends_at'], $timezone)->utc() : null,
            ))
            ->all();

        return new EventData(
            user_id: $this->user()->id,
            organization_id: $organizationId,
            title: $this->validated('title'),
            description: $this->validated('description'),
            period: new DateRange(
                start: CarbonImmutable::parse($this->validated('starts_at'), $timezone)->utc(),
                end: CarbonImmutable::parse($this->validated('ends_at'), $timezone)->utc(),
            ),
            timezone: $timezone,
            venue_name: $this->validated('venue_name'),
            address: $this->validated('address'),
            zip: $this->validated('zip'),
            map_url: $this->validated('map_url'),
            ticket_types: $ticketTypes,
        );
    }
}
