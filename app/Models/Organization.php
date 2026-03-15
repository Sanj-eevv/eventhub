<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrganizationStatus;
use App\Traits\HasAppUuid;
use App\Traits\HasSlug;
use Carbon\CarbonImmutable;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Builder;
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
 * @property CarbonImmutable|null $verified_at
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static OrganizationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Organization newModelQuery()
 * @method static Builder<static>|Organization newQuery()
 * @method static Builder<static>|Organization onlyTrashed()
 * @method static Builder<static>|Organization query()
 * @method static Builder<static>|Organization whereContactAddress($value)
 * @method static Builder<static>|Organization whereContactEmail($value)
 * @method static Builder<static>|Organization whereCreatedAt($value)
 * @method static Builder<static>|Organization whereDeletedAt($value)
 * @method static Builder<static>|Organization whereDescription($value)
 * @method static Builder<static>|Organization whereId($value)
 * @method static Builder<static>|Organization whereSlug($value)
 * @method static Builder<static>|Organization whereStatus($value)
 * @method static Builder<static>|Organization whereTitle($value)
 * @method static Builder<static>|Organization whereUpdatedAt($value)
 * @method static Builder<static>|Organization whereUuid($value)
 * @method static Builder<static>|Organization whereVerifiedAt($value)
 * @method static Builder<static>|Organization withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Organization withoutTrashed()
 * @mixin \Eloquent
 */
final class Organization extends Model
{
    use HasAppUuid;
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'contact_address',
        'contact_email',
        'status',
        'verified_at',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected function casts(): array
    {
        return [
            'status' => OrganizationStatus::class,
            'verified_at' => 'datetime',
        ];
    }
}
