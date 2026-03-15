<?php

declare(strict_types=1);

use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

if ( ! function_exists('userTime')) {
    function userTime(DateTimeInterface|string $dateTime, ?string $timezone = null): CarbonInterface
    {
        if (is_string($dateTime)) {
            $dateTime = Carbon::parse($dateTime);
        }
        $tz = $timezone ? auth()->user()?->timezone : config('app.timezone');

        return $dateTime->timezone($tz);
    }
}

if ( ! function_exists('formatUserTime')) {
    function formatUserTime(DateTimeInterface|string $dateTime, string $format = 'd M Y, H:i', ?string $timezone = null): string
    {
        return userTime($dateTime, $timezone)->format($format);
    }
}

if ( ! function_exists('omit')) {
    function omit(string|array $keys, array $data): array
    {
        foreach (Arr::wrap($keys) as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
