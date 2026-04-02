<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\SettingsData;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final class SettingsService
{
    public const string CACHE_KEY = 'app_settings';

    public function __construct(private readonly CacheRepository $cache) {}

    public function get(): SettingsData
    {
        $data = $this->cache->rememberForever(self::CACHE_KEY, fn (): array => SettingsData::fromDatabase()->toArray());

        return SettingsData::fromArray($data);
    }
}
