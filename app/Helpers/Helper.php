<?php

declare(strict_types=1);

use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

if ( ! function_exists('userTime')) {
    function userTime(DateTimeInterface|string $dateTime, ?string $timezone = null): CarbonInterface
    {
        $carbon = is_string($dateTime) ? Date::parse($dateTime) : Date::instance($dateTime);

        $tz = $timezone ?? (string) (config('app.timezone'));

        return $carbon->timezone($tz);
    }
}

if ( ! function_exists('formatUserTime')) {
    function formatUserTime(DateTimeInterface|string $dateTime, string $format = 'd M Y, H:i', ?string $timezone = null): string
    {
        return userTime($dateTime, $timezone)->format($format);
    }
}

if ( ! function_exists('omit')) {
    /**
     * @param  string|string[]  $keys
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    function omit(string|array $keys, array $data): array
    {
        foreach (Arr::wrap($keys) as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
