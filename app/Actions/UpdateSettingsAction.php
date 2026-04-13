<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\SettingsData;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class UpdateSettingsAction
{
    public function __construct(private CacheRepository $cache) {}

    public function __invoke(SettingsData $data): void
    {
        Setting::query()->upsert(
            values: collect($data->toArray())
                ->map(fn (mixed $value, string $key): array => ['key' => $key, 'value' => $value])
                ->values()
                ->all(),
            uniqueBy: ['key'],
            update: ['value'],
        );

        $this->cache->forget(SettingsService::CACHE_KEY);
    }
}
