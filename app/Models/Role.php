<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Role extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public static function defaultRole(): ?Role
    {
        return Role::query()->where('slug', 'user')->first();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
