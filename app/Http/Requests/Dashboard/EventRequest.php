<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\DataTransferObjects\EventData;
use App\DataTransferObjects\TicketTypeData;
use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\Organization;
use App\Models\TicketType;
use App\Models\User;
use App\Rules\EndsOnDifferentCalendarDay;
use App\Support\DateFormat;
use App\ValueObjects\DateRange;
use Carbon\CarbonImmutable;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class EventRequest extends FormRequest
{
    public function __construct(private AuthManager $authManager) {}

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
            foreach ($this->array('ticket_types') as $index => $ticketType) {
                /** @var array{sale_ends_at?: mixed, sale_starts_at?: mixed} $ticketType */
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

            $submittedUuids = collect($this->array('ticket_types'))->pluck('uuid');

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

        /**
         * @var array{
         * timezone: string,
         * title: string,
         * description: string,
         * organization_uuid: string,
         * venue_name: string,
         * address: string,
         * zip: string,
         * map_url?: string,
         * starts_at: string,
         * ends_at: string,
         * ticket_types: array<int, array{name: string, description: string, price: int, capacity: int, uuid ?: string, max_per_user?: int, sale_starts_at?: string, sale_ends_at?: string}>
         * } $validatedData
         */
        $validatedData = $this->validated();
        $timezone = $validatedData['timezone'];
        /** @var int $organizationId */
        $organizationId = Organization::query()->where('uuid', $validatedData['organization_uuid'])->value('id');
        $validatedTicketTypes = $validatedData['ticket_types'];
        $ticketTypes = collect($validatedTicketTypes)
            ->map(fn (array $type, int $index): TicketTypeData => new TicketTypeData(
                name: $type['name'],
                description: $type['description'],
                price: (int) round($type['price'] * 100),
                capacity: $type['capacity'],
                sort_order: $index,
                uuid: $type['uuid'] ?? '',
                max_per_user: $type['max_per_user'] ?? null,
                sale_starts_at: isset($type['sale_starts_at']) ? CarbonImmutable::parse($type['sale_starts_at'], $timezone)->utc() : null,
                sale_ends_at: isset($type['sale_ends_at']) ? CarbonImmutable::parse($type['sale_ends_at'], $timezone)->utc() : null,
            ))
            ->all();

        /** @var User $user */
        $user = $this->authManager->user();

        return new EventData(
            user_id: $user->id,
            organization_id: $organizationId,
            title: $validatedData['title'],
            description: $validatedData['description'],
            period: new DateRange(
                start: CarbonImmutable::parse($validatedData['starts_at'], $timezone)->utc(),
                end: CarbonImmutable::parse($validatedData['ends_at'], $timezone)->utc(),
            ),
            timezone: $timezone,
            venue_name: $validatedData['venue_name'],
            address: $validatedData['address'],
            zip: $validatedData['zip'],
            map_url: $validatedData['map_url'] ?? '',
            ticket_types: $ticketTypes,
        );
    }
}
