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
 * @method static Builder<static>|\Illuminate\Support\Facades\Event newModelQuery()
 * @method static Builder<static>|\Illuminate\Support\Facades\Event newQuery()
 * @method static Builder<static>|\Illuminate\Support\Facades\Event onlyTrashed()
 * @method static Builder<static>|\Illuminate\Support\Facades\Event query()
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereCreatedAt($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereDeletedAt($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereDescription($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereEndsAt($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereId($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereOrganizationId($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereSlug($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereStartsAt($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereStatus($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereTimezone($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereTitle($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereUpdatedAt($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereUserId($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event whereUuid($value)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|\Illuminate\Support\Facades\Event withoutTrashed()
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
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

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
