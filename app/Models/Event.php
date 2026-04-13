<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\EventBuilder;
use App\Enums\EventStatus;
use App\Traits\HasAppUuid;
use App\Traits\HasSlug;
use Carbon\CarbonImmutable;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $organization_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable $ends_at
 * @property string $timezone
 * @property string $venue_name
 * @property string $address
 * @property string $zip
 * @property string|null $map_url
 * @property EventStatus $status
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Organization $organization
 * @property-read User $user
 * @property-read Collection<int, TicketType> $ticketTypes
 * @property-read Collection<int, Order> $orders
 * @property-read Collection<int, Ticket> $tickets
 * @property-read Collection<int, Media> $media
 * @property-read Media|null $coverImage
 *
 * @method static EventFactory factory($count = null, $state = [])
 *
 * @mixin Model
 */
#[UseEloquentBuilder(EventBuilder::class)]
#[Fillable([
    'user_id',
    'organization_id',
    'title',
    'slug',
    'description',
    'starts_at',
    'ends_at',
    'status',
    'timezone',
    'venue_name',
    'address',
    'zip',
    'map_url',
])]
final class Event extends Model
{
    use HasAppUuid;
    /** @use HasFactory<EventFactory> */
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    /** @return HasMany<TicketType, $this> */
    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return HasMany<Ticket, $this> */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return MorphMany<Media, $this> */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    /** @return MorphOne<Media, $this> */
    public function coverImage(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')->where('is_cover', true);
    }

    protected function casts(): array
    {
        return [
            'status' => EventStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
}
