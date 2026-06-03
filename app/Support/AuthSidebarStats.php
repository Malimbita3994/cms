<?php

namespace App\Support;

use App\Models\CaseStudy;
use App\Models\Insight;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class AuthSidebarStats
{
    /**
     * @return array{stats: array{projects: int, services: int, case_studies: int, insights: int}, brand: string}
     */
    public static function cached(): array
    {
        return Cache::remember('auth:sidebar', now()->addMinutes(10), static function (): array {
            return [
                'stats' => [
                    'projects' => static::safeCount(PortfolioProject::class),
                    'services' => static::safeCount(Service::class),
                    'case_studies' => static::safeCount(CaseStudy::class),
                    'insights' => static::safeCount(Insight::class),
                ],
                'brand' => static::safeBrand(),
            ];
        });
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private static function safeCount(string $modelClass): int
    {
        $table = (new $modelClass)->getTable();

        if (! Schema::hasTable($table)) {
            return 0;
        }

        try {
            return (int) $modelClass::query()->count();
        } catch (Throwable) {
            return 0;
        }
    }

    private static function safeBrand(): string
    {
        if (! Schema::hasTable('site_settings')) {
            return (string) config('app.name', 'Portfolio CMS');
        }

        try {
            return SiteSetting::query()->value('app_name')
                ?? config('app.name', 'Portfolio CMS');
        } catch (Throwable) {
            return (string) config('app.name', 'Portfolio CMS');
        }
    }
}
