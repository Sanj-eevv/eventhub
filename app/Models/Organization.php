<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrganizationStatus;
use App\Traits\HasAppUuid;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Organization extends Model
{
    use HasAppUuid;
    use HasFactory;
    use HasSlug;

    /**
     * @var list<string>
     */

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
