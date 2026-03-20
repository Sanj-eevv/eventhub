<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static Builder<static>|Permission newModelQuery()
 * @method static Builder<static>|Permission newQuery()
 * @method static Builder<static>|Permission query()
 * @method static Builder<static>|Permission whereCreatedAt($value)
 * @method static Builder<static>|Permission whereDescription($value)
 * @method static Builder<static>|Permission whereId($value)
 * @method static Builder<static>|Permission whereName($value)
 * @method static Builder<static>|Permission whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Permission extends Model
{
    protected $fillable = ['name', 'description'];

    /** @return array<string, list<array{id: int, name: string, description: string}>> */
    public static function grouped(?Role $role = null): array
    {
        $permissions = $role
            ? $role->permissions
            : self::query()->select('id', 'name', 'description')->get();

        return $permissions->mapToGroups(function (Permission $permission): array {
            [$entity, $action] = explode(':', $permission->name, 2);

            return [$entity => ['id' => $permission->id, 'name' => $action, 'description' => $permission->description]];
        })->toArray();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }
}
