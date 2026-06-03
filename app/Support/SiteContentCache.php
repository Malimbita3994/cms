<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class SiteContentCache
{
    /** Bumped when site bundle shape changes (e.g. list fields must be JSON arrays). */
    public const KEY = 'api:v1:site:3';

    public const LIST_KEYS = [
        'careerTimeline',
        'skills',
        'projects',
        'services',
        'insights',
        'caseStudies',
    ];

    public static function remember(callable $builder): array
    {
        $seconds = (int) config('cms.site_api_cache_seconds', 300);
        $cached = Cache::get(self::KEY);

        if (is_string($cached)) {
            $decoded = json_decode($cached, true);
            if (is_array($decoded) && ! self::hasCorruptLists($decoded)) {
                return $decoded;
            }
        } elseif (is_array($cached)) {
            $normalized = self::normalizePayload($cached);
            if (! self::hasCorruptLists($normalized)) {
                return $normalized;
            }
        }

        self::flush();

        $payload = self::normalizePayload($builder());
        Cache::put(self::KEY, json_encode($payload), now()->addSeconds($seconds));

        return $payload;
    }

    public static function flush(): void
    {
        Cache::forget(self::KEY);
        Cache::forget('api:v1:site');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function hasCorruptLists(array $payload): bool
    {
        foreach (self::LIST_KEYS as $key) {
            if (isset($payload[$key]) && ! is_array($payload[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function normalizePayload(array $payload): array
    {
        foreach (self::LIST_KEYS as $key) {
            if (! isset($payload[$key])) {
                continue;
            }

            $payload[$key] = self::normalizeList($payload[$key]);
        }

        return $payload;
    }

    /**
     * @return array<int, mixed>
     */
    public static function normalizeList(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if ($value instanceof Collection) {
            return $value->values()->all();
        }

        return [];
    }
}
