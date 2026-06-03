<?php

namespace App\Support;

use App\Models\CaseStudy;
use App\Models\Insight;
use App\Models\PortfolioProject;
use App\Models\Profile;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

final class DashboardMetrics
{
    /**
     * @return array{
     *     kpis: array<string, int>,
     *     recent: array<int, array{type: string, title: string, when: string}>,
     *     activityTrend: array<int, array{day: string, total: int}>,
     *     contentHealth: array<string, int|bool>,
     *     branding: array{app_name: string, profile_name: string}
     * }
     */
    public static function cached(): array
    {
        $seconds = (int) config('cms.dashboard_cache_seconds', 300);

        return Cache::remember('dashboard:bundle', now()->addSeconds($seconds), static function (): array {
            $kpis = [
                'projects' => PortfolioProject::query()->count(),
                'services' => Service::query()->count(),
                'insights' => Insight::query()->count(),
                'case_studies' => CaseStudy::query()->count(),
            ];

            $recent = collect()
                ->merge(
                    PortfolioProject::query()
                        ->latest('updated_at')
                        ->limit(2)
                        ->get(['title', 'updated_at'])
                        ->map(fn (PortfolioProject $p): array => [
                            'type' => 'Project',
                            'title' => $p->title,
                            'sort_at' => $p->updated_at?->timestamp ?? 0,
                            'when' => $p->updated_at?->diffForHumans() ?? 'just now',
                        ]),
                )
                ->merge(
                    Insight::query()
                        ->latest('updated_at')
                        ->limit(2)
                        ->get(['title', 'updated_at'])
                        ->map(fn (Insight $i): array => [
                            'type' => 'Insight',
                            'title' => $i->title,
                            'sort_at' => $i->updated_at?->timestamp ?? 0,
                            'when' => $i->updated_at?->diffForHumans() ?? 'just now',
                        ]),
                )
                ->merge(
                    Service::query()
                        ->latest('updated_at')
                        ->limit(2)
                        ->get(['title', 'updated_at'])
                        ->map(fn (Service $s): array => [
                            'type' => 'Service',
                            'title' => $s->title,
                            'sort_at' => $s->updated_at?->timestamp ?? 0,
                            'when' => $s->updated_at?->diffForHumans() ?? 'just now',
                        ]),
                )
                ->sortByDesc('sort_at')
                ->values()
                ->take(6)
                ->map(fn (array $row): array => [
                    'type' => $row['type'],
                    'title' => $row['title'],
                    'when' => $row['when'],
                ])
                ->all();

            $since = now()->subDays(6)->startOfDay();
            $days = collect(range(6, 0))
                ->mapWithKeys(fn (int $offset): array => [now()->subDays($offset)->toDateString() => 0]);

            $sources = collect([
                PortfolioProject::query()->selectRaw('DATE(updated_at) as day, COUNT(*) as total')->where('updated_at', '>=', $since)->groupBy('day')->pluck('total', 'day'),
                Insight::query()->selectRaw('DATE(updated_at) as day, COUNT(*) as total')->where('updated_at', '>=', $since)->groupBy('day')->pluck('total', 'day'),
                Service::query()->selectRaw('DATE(updated_at) as day, COUNT(*) as total')->where('updated_at', '>=', $since)->groupBy('day')->pluck('total', 'day'),
            ]);

            $activityTrend = $days
                ->map(fn (int $base, string $day): int => $sources->sum(static fn ($source): int => (int) ($source[$day] ?? 0)))
                ->map(fn (int $total, string $day): array => [
                    'day' => Carbon::parse($day)->format('D'),
                    'total' => $total,
                ])
                ->values()
                ->all();

            $hasProfile = Profile::query()->exists();
            $hasSettings = SiteSetting::query()->exists();
            $healthyBlocks = collect([
                'projects' => $kpis['projects'],
                'services' => $kpis['services'],
                'insights' => $kpis['insights'],
            ])->filter(static fn (int $count): bool => $count > 0)->count();

            return [
                'kpis' => $kpis,
                'recent' => $recent,
                'activityTrend' => $activityTrend,
                'contentHealth' => [
                    'score' => (int) round((($healthyBlocks + (int) $hasProfile + (int) $hasSettings) / 5) * 100),
                    'has_profile' => $hasProfile,
                    'has_settings' => $hasSettings,
                    'active_blocks' => $healthyBlocks,
                ],
                'branding' => [
                    'app_name' => SiteSetting::query()->value('app_name') ?? 'Portfolio CMS',
                    'profile_name' => Profile::query()->value('name') ?? 'Admin',
                ],
            ];
        });
    }

    public static function flush(): void
    {
        Cache::forget('dashboard:bundle');
        Cache::forget('auth:sidebar');
    }
}
