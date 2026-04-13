<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\UserBuilder;
use App\Contracts\Authorizable;
use App\Enums\PreservedRoleList;
use App\Notifications\QueueableVerifyEmail;
use App\Traits\HasAppUuid;
use BackedEnum;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $uuid
 * @property int $role_id
 * @property int|null $organization_id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Organization|null $organization
 * @property-read Role $role
 * @property-read Collection<int, Event> $events
 * @property-read int|null $events_count
 *
 * @method static UserFactory factory( $count = null, $state = [] )
 *
 * @mixin Model
 */
#[UseEloquentBuilder(UserBuilder::class)]
#[Fillable([
    'role_id',
    'organization_id',
    'name',
    'email',
    'password',
])]
#[Hidden([
    'password',
    'remember_token',
])]
final class User extends Authenticatable implements Authorizable, MustVerifyEmail
{
    use HasAppUuid;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;
    use SoftDeletes;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueueableVerifyEmail());
    }

    /** @return HasMany<Event, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<Role, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** @return Collection<int, string> */
    public function getAllPermissions(): Collection
    {
        /** @var Collection<int, string> */
        return once(fn (): Collection => $this->role->permissions->pluck('name'));
    }

    public function hasPermission(BackedEnum $permission): bool
    {
        return $this->getAllPermissions()->contains(fn (string $name): bool => $name === $permission->value);
    }

    public function hasAllPermissions(BackedEnum ...$permissions): bool
    {
        $normalised = collect($permissions)->map(fn (BackedEnum $permission): int|string => $permission->value);

        return $normalised->diff($this->getAllPermissions())->isEmpty();
    }

    public function hasAnyPermission(BackedEnum ...$permissions): bool
    {
        $normalised = collect($permissions)->map(fn (BackedEnum $permission): string => (string) $permission->value);

        return $this->getAllPermissions()->intersect($normalised->values())->isNotEmpty();
    }

    public function hasAnyRole(PreservedRoleList ...$roles): bool
    {
        $slugs = collect($roles)->map(fn (PreservedRoleList $role) => $role->value);

        return $slugs->contains($this->role->slug);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
