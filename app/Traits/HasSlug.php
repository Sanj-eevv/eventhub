<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (Model $model): void {
            $sluggableColumn = static::getSluggableColumn();
            $slug = Str::slug((string) ($model->getAttribute($sluggableColumn)));
            $initialSlug = $slug;
            $iteration = 0;
            $slugColumnName = static::getSlugColumnName();
            while ($model::query()->where($slugColumnName, $slug)->exists()) {
                $iteration++;
                $slug = sprintf('%s-%d', $initialSlug, $iteration);
            }

            $model->setAttribute($slugColumnName, $slug);
        });

    }

    public static function getSluggableColumn(): string
    {
        return 'title';
    }

    public static function getSlugColumnName(): string
    {
        return 'slug';
    }
}
