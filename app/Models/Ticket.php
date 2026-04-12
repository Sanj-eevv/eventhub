<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\TicketBuilder;
use App\Enums\TicketStatus;
use App\Traits\HasAppUuid;
use Carbon\CarbonImmutable;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property int $order_id
 * @property int $ticket_type_id
 * @property int $event_id
 * @property int $user_id
 * @property string $booking_reference
 * @property string $attendee_name
 * @property string $attendee_email
 * @property TicketStatus $status
 * @property string|null $qr_code_path
 * @property CarbonImmutable|null $checked_in_at
 * @property int|null $checked_in_by
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Order $order
 * @property-read TicketType $ticketType
 * @property-read Event $event
 * @property-read User $user
 * @property-read User|null $checkedInBy
 *
 * @method static TicketFactory factory($count = null, $state = [])
 */
#[UseEloquentBuilder(TicketBuilder::class)]
#[Fillable([
    'order_id',
    'ticket_type_id',
    'event_id',
    'user_id',
    'booking_reference',
    'attendee_name',
    'attendee_email',
    'status',
    'qr_code_path',
    'checked_in_at',
    'checked_in_by',
])]
final class Ticket extends Model
{
    use HasAppUuid;
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'checked_in_at' => 'datetime',
        ];
    }
}
