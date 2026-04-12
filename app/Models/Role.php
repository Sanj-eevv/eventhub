<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\RoleBuilder;
use App\Enums\PreservedRoleList;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseEloquentBuilder(RoleBuilder::class)]
#[Fillable([
    'name',
    'slug',
    'description',
    'preserved',
])]
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property bool $preserved
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static RoleFactory factory($count = null, $state = [])
 * @method static RoleBuilder<static>|Role newModelQuery()
 * @method static RoleBuilder<static>|Role newQuery()
 * @method static RoleBuilder<static>|Role query()
 * @method static RoleBuilder<static>|Role forIndex()
 * @method static RoleBuilder<static>|Role search(?string $search)
 * @method static RoleBuilder<static>|Role sortBy(?array $columns)
 * @method static RoleBuilder<static>|Role whereCreatedAt($value)
 * @method static RoleBuilder<static>|Role whereDescription($value)
 * @method static RoleBuilder<static>|Role whereId($value)
 * @method static RoleBuilder<static>|Role whereName($value)
 * @method static RoleBuilder<static>|Role wherePreserved($value)
 * @method static RoleBuilder<static>|Role whereSlug($value)
 * @method static RoleBuilder<static>|Role whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Role extends Model
{
    use HasFactory;
    use HasSlug;

    public static function superAdminRole(): self
    {
        return self::query()->where('slug', PreservedRoleList::SuperAdmin->value)->firstOrFail();
    }

    public static function adminRole(): self
    {
        return self::query()->where('slug', PreservedRoleList::Admin->value)->firstOrFail();
    }

    public static function organizationAdminRole(): self
    {
        return self::query()->where('slug', PreservedRoleList::OrganizationAdmin->value)->firstOrFail();
    }

    public static function userRole(): self
    {
        return self::query()->where('slug', PreservedRoleList::User->value)->firstOrFail();
    }

    public static function getSluggableColumn(): string
    {
        return 'name';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    protected function casts(): array
    {
        return [
            'preserved' => 'boolean',
        ];
    }
}
