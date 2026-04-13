<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\OrganizationBuilder;
use App\Enums\OrganizationStatus;
use App\Traits\HasAppUuid;
use App\Traits\HasSlug;
use Carbon\CarbonImmutable;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $contact_address
 * @property string $contact_email
 * @property OrganizationStatus $status
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @property-read Collection<int, Event> $events
 * @property-read int|null $events_count
 *
 * @method static OrganizationFactory factory($count = null, $state = [])
 *
 * @mixin Model
 */
#[UseEloquentBuilder(OrganizationBuilder::class)]
#[Fillable([
    'title',
    'slug',
    'description',
    'contact_address',
    'contact_email',
    'status',
])]
final class Organization extends Model
{
    use HasAppUuid;
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<Event, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    protected function casts(): array
    {
        return [
            'status' => OrganizationStatus::class,
        ];
    }
}
