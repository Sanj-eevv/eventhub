<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\OrderBuilder;
use App\Enums\OrderStatus;
use App\Enums\RefundStatus;
use App\Traits\HasAppUuid;
use Carbon\CarbonImmutable;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $event_id
 * @property OrderStatus $status
 * @property string $currency
 * @property int $subtotal
 * @property int $total
 * @property string|null $stripe_payment_intent_id
 * @property string|null $stripe_client_secret
 * @property string|null $stripe_refund_id
 * @property RefundStatus|null $refund_status
 * @property CarbonImmutable|null $reserved_at
 * @property CarbonImmutable|null $expires_at
 * @property CarbonImmutable|null $paid_at
 * @property CarbonImmutable|null $cancelled_at
 * @property CarbonImmutable|null $refunded_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Event $event
 * @property-read Collection<int, Ticket> $tickets
 *
 * @method static OrderFactory factory($count = null, $state = [])
 */
#[UseEloquentBuilder(OrderBuilder::class)]
#[Fillable([
    'user_id',
    'event_id',
    'status',
    'currency',
    'subtotal',
    'total',
    'stripe_payment_intent_id',
    'stripe_client_secret',
    'stripe_refund_id',
    'refund_status',
    'reserved_at',
    'expires_at',
    'paid_at',
    'cancelled_at',
    'refunded_at',
])]
final class Order extends Model
{
    use HasAppUuid;
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'refund_status' => RefundStatus::class,
            'subtotal' => 'integer',
            'total' => 'integer',
            'reserved_at' => 'datetime',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }
}
