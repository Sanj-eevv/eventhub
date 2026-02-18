<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PreservedRoleList;
use App\Traits\HasAppUuid;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Role extends Model
{
    use HasAppUuid;
    use HasFactory;
    use HasSlug;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'preserved',
    ];

    public static function SuperAdminRole(): ?Role
    {
        return Role::query()->where('slug', PreservedRoleList::SUPER_ADMIN->value)->first();
    }

    public static function UserRole(): ?Role
    {
        return Role::query()->where('slug', PreservedRoleList::USER->value)->first();
    }

    public static function getSluggableColumn(): string
    {
        return 'name';
    }

    public static function organizationAdminRole(): ?Role
    {
        return Role::query()->where('slug', PreservedRoleList::ORGANIZATION_ADMIN->value)->first();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preserved' => 'boolean',
        ];
    }

}
