<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\TicketTypeBuilder;
use App\Models\Scopes\SortByOrderScope;
use Database\Factories\TicketTypeFactory;
use App\Traits\HasAppUuid;
use App\Traits\HasSlug;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $uuid
 * @property int $event_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $price
 * @property int $capacity
 * @property int|null $max_per_user
 * @property int $sort_order
 * @property CarbonImmutable|null $sale_starts_at
 * @property CarbonImmutable|null $sale_ends_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Event $event
 * @property-read Collection<int, Ticket> $tickets
 */
#[ScopedBy(SortByOrderScope::class)]
#[UseEloquentBuilder(TicketTypeBuilder::class)]
#[Fillable([
    'event_id',
    'name',
    'slug',
    'price',
    'description',
    'capacity',
    'max_per_user',
    'sort_order',
    'sale_starts_at',
    'sale_ends_at',
])]
final class TicketType extends Model
{
    use HasAppUuid;
    /** @use HasFactory<TicketTypeFactory> */
    use HasFactory;
    use HasSlug;

    public static function getSluggableColumn(): string
    {
        return 'name';
    }

    /** @return BelongsTo<Event, $this> */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /** @return HasMany<Ticket, $this> */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'capacity' => 'integer',
            'max_per_user' => 'integer',
            'sort_order' => 'integer',
            'sale_starts_at' => 'datetime',
            'sale_ends_at' => 'datetime',
        ];
    }
}
