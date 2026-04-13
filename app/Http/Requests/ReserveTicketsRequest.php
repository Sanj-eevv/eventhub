<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DataTransferObjects\TicketItemData;
use Illuminate\Foundation\Http\FormRequest;

final class ReserveTicketsRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.ticket_type_uuid' => ['required', 'string', 'exists:ticket_types,uuid'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /** @return array<int, TicketItemData> */
    public function toDto(): array
    {
        /** @var array<int, array{ticket_type_uuid: string, quantity: int}> $items */
        $items = $this->validated('items');

        return collect($items)
            ->map(fn (array $item): TicketItemData => new TicketItemData(
                ticketTypeUuid: $item['ticket_type_uuid'],
                quantity: $item['quantity'],
            ))
            ->all();
    }
}
