<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\QueueableVerifyEmail;
use App\Traits\HasAppUuid;
use BackedEnum;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;

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

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function getAllPermissions()
    {
        if (Auth::user()->id === $this->id && Context::hasHidden('permissions')) {
            return Context::getHidden('permissions');
        }

        return $this
            ->role()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->map(fn (string $item): string => mb_strtolower($item));
    }

    public function hasPermission(string|BackedEnum|array $permission): bool
    {
        $permissions = Arr::wrap($permission);
        foreach ($permissions as &$permission) {
            $permission = mb_strtolower($permission instanceof BackedEnum ? $permission->value : $permission);
        }

        return collect($permission)->diff($this->getAllPermissions())->isEmpty();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        $perms = array_map(function ($value) {
            if ($value instanceof BackedEnum) {
                $value = $value->value;
            }

            return mb_strtolower($value);
        }, $permissions);

        return $this->getAllPermissions()->intersect($perms)->isNotEmpty();
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
