<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\SettingsData;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final class UpdateSettingsAction
{
    public function __construct(private readonly CacheRepository $cache) {}

    public function execute(SettingsData $data): void
    {
        Setting::query()->upsert(
            values: collect($data->toArray())
                ->map(fn (int $value, string $key): array => compact('key', 'value'))
                ->values()
                ->all(),
            uniqueBy: ['key'],
            update: ['value'],
        );

        $this->cache->forget(SettingsService::CACHE_KEY);
    }
}
