<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\UserBuilder;
use App\Notifications\QueueableVerifyEmail;
use App\Traits\HasAppUuid;
use BackedEnum;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
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
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User onlyTrashed()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt( $value )
 * @method static Builder<static>|User whereDeletedAt( $value )
 * @method static Builder<static>|User whereEmail( $value )
 * @method static Builder<static>|User whereEmailVerifiedAt( $value )
 * @method static Builder<static>|User whereId( $value )
 * @method static Builder<static>|User whereName( $value )
 * @method static Builder<static>|User whereOrganizationId( $value )
 * @method static Builder<static>|User wherePassword( $value )
 * @method static Builder<static>|User whereRememberToken( $value )
 * @method static Builder<static>|User whereRoleId( $value )
 * @method static Builder<static>|User whereUpdatedAt( $value )
 * @method static Builder<static>|User whereUuid( $value )
 * @method static Builder<static>|User withTrashed( bool $withTrashed = true )
 * @method static Builder<static>|User withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseEloquentBuilder(UserBuilder::class)]
final class User extends Authenticatable implements MustVerifyEmail
{
    use HasAppUuid;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'role_id',
        'organization_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueueableVerifyEmail());
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** @return Collection<int, string> */
    public function getAllPermissions(): Collection
    {
        $this->loadMissing('role.permissions');

        return $this->role->permissions
            ->pluck('name')
            ->map(fn (string $name): string => mb_strtolower($name));
    }

    public function hasPermission(string|BackedEnum $permission): bool
    {
        $value = $permission instanceof BackedEnum ? $permission->value : $permission;

        return $this->getAllPermissions()->contains($value);
    }

    public function hasAllPermissions(array $permissions): bool
    {
        $normalised = collect($permissions)
            ->map(fn ($permission) => $permission instanceof BackedEnum ? $permission->value : $permission);

        return $normalised->diff($this->getAllPermissions())->isEmpty();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        $normalised = collect($permissions)
            ->map(fn ($permission) => $permission instanceof BackedEnum ? $permission->value : $permission);

        return $this->getAllPermissions()->intersect($normalised)->isNotEmpty();
    }

    public function hasAnyRole(string|array $roles): bool
    {
        return in_array($this->role->slug, Arr::wrap($roles));
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
