@push('styles')
    @vite('resources/css/filament/dashboard.css')
@endpush

<x-filament-panels::page>
    @php
        $firstName = explode(' ', $branding['profile_name'] ?? 'Admin')[0];
        $points = collect($chartPoints);
        $maxChart = max(1, $points->max('total') ?? 1);
        $chartW = 600;
        $chartH = 160;
        $padX = 24;
        $padY = 16;
        $innerW = $chartW - $padX * 2;
        $innerH = $chartH - $padY * 2;
        $count = max(1, $points->count());
        $coords = $points->values()->map(function ($p, $i) use ($count, $innerW, $innerH, $maxChart, $padX, $padY, $chartH) {
            $x = $padX + ($count > 1 ? ($i / ($count - 1)) * $innerW : $innerW / 2);
            $y = $padY + $innerH - (($p['total'] / $maxChart) * $innerH);

            return round($x, 1).','.round($y, 1);
        })->implode(' ');
        $areaCoords = $coords.' '.round($padX + $innerW, 1).','.round($chartH - $padY, 1).' '.round($padX, 1).','.round($chartH - $padY, 1);
        $readiness = (int) ($contentHealth['score'] ?? 0);
        $ringOffset = 113 - (113 * $readiness / 100);
        $totalItems = ($kpis['projects'] ?? 0) + ($kpis['services'] ?? 0) + ($kpis['insights'] ?? 0);
    @endphp

    <div class="saas-workspace" data-layout="dashboard">
        <div class="saas-grid">

            {{-- Hero + quick actions --}}
            <header class="saas-widget saas-widget--hero saas-grid__8">
                <h2 class="saas-hero__title">Good day, {{ $firstName }} 👋</h2>
                <p class="saas-hero__desc">{{ $branding['app_name'] }} — analytics workspace and publishing status at a glance.</p>
                <div class="saas-hero__meta">
                    <div>
                        <span>Today</span>
                        <strong>{{ now()->format('M j, Y') }}</strong>
                    </div>
                    <div>
                        <span>Last sync</span>
                        <strong>{{ $lastUpdated ?: now()->format('H:i:s') }}</strong>
                    </div>
                    <div>
                        <span>Readiness</span>
                        <strong>{{ $readiness }}%</strong>
                    </div>
                </div>
            </header>

            <aside class="saas-widget saas-widget--actions saas-grid__4" aria-label="Quick actions">
                <p class="saas-actions__label">Quick actions</p>
                <nav class="saas-actions__list">
                    <a href="{{ url('/admin/portfolio-projects/create') }}" class="saas-actions__item">
                        <span class="saas-actions__icon saas-actions__icon--orange">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        </span>
                        Add new project
                        <span class="saas-actions__chevron">›</span>
                    </a>
                    <a href="{{ url('/admin/services/create') }}" class="saas-actions__item">
                        <span class="saas-actions__icon saas-actions__icon--blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        </span>
                        Add new service
                        <span class="saas-actions__chevron">›</span>
                    </a>
                    <a href="{{ url('/admin/insights/create') }}" class="saas-actions__item">
                        <span class="saas-actions__icon saas-actions__icon--green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        </span>
                        New insight
                        <span class="saas-actions__chevron">›</span>
                    </a>
                    <a href="{{ $links['site'] }}" target="_blank" rel="noopener" class="saas-actions__item">
                        <span class="saas-actions__icon saas-actions__icon--purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/></svg>
                        </span>
                        View live site
                        <span class="saas-actions__chevron">›</span>
                    </a>
                </nav>
            </aside>

            {{-- Analytics cards --}}
            <section class="saas-analytics" aria-label="Analytics overview">
                <article class="saas-metric">
                    <div class="saas-metric__head">
                        <span class="saas-metric__icon saas-metric__icon--orange">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7.5V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v1.5"/><path d="M3 7.5h18v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-12z"/></svg>
                        </span>
                        <a href="{{ url('/admin/portfolio-projects') }}" class="saas-metric__arrow" aria-label="Projects">›</a>
                    </div>
                    <div class="saas-metric__value">{{ $kpis['projects'] ?? 0 }}</div>
                    <div class="saas-metric__label">Projects</div>
                    <div class="saas-metric__hint">Portfolio entries</div>
                </article>

                <article class="saas-metric">
                    <div class="saas-metric__head">
                        <span class="saas-metric__icon saas-metric__icon--blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/></svg>
                        </span>
                        <a href="{{ url('/admin/services') }}" class="saas-metric__arrow" aria-label="Services">›</a>
                    </div>
                    <div class="saas-metric__value">{{ $kpis['services'] ?? 0 }}</div>
                    <div class="saas-metric__label">Services</div>
                    <div class="saas-metric__hint">Offerings</div>
                </article>

                <article class="saas-metric">
                    <div class="saas-metric__head">
                        <span class="saas-metric__icon saas-metric__icon--green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v13A2.5 2.5 0 0 1 17.5 21h-11A2.5 2.5 0 0 1 4 18.5v-13z"/></svg>
                        </span>
                        <a href="{{ url('/admin/insights') }}" class="saas-metric__arrow" aria-label="Insights">›</a>
                    </div>
                    <div class="saas-metric__value">{{ $kpis['insights'] ?? 0 }}</div>
                    <div class="saas-metric__label">Insights</div>
                    <div class="saas-metric__hint">Articles</div>
                </article>

                <article class="saas-metric">
                    <div class="saas-metric__head">
                        <div class="saas-metric__ring">
                            <svg viewBox="0 0 40 40" aria-hidden="true">
                                <circle cx="20" cy="20" r="18" fill="none" stroke="#252a35" stroke-width="4"/>
                                <circle cx="20" cy="20" r="18" fill="none" stroke="#f59e0b" stroke-width="4" stroke-dasharray="113" stroke-dashoffset="{{ $ringOffset }}" stroke-linecap="round" transform="rotate(-90 20 20)"/>
                            </svg>
                            <div>
                                <div class="saas-metric__value" style="font-size:1.35rem;">{{ $readiness }}%</div>
                                <div class="saas-metric__label">Readiness</div>
                            </div>
                        </div>
                    </div>
                    <div class="saas-metric__hint">{{ $contentHealth['active_blocks'] ?? 0 }} of 3 blocks live</div>
                </article>
            </section>

            {{-- Chart analytics --}}
            <section class="saas-widget saas-grid__8">
                <div class="saas-widget__head">
                    <div>
                        <h3>Content analytics</h3>
                        <p>Edits across projects, services, and insights — last 7 days</p>
                    </div>
                    <span class="saas-widget__link">This month</span>
                </div>
                <div class="saas-widget__body saas-widget__body--flush">
                    <div class="saas-chart" role="img" aria-label="Activity trend chart">
                        <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="saasChartFill" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.35"/>
                                    <stop offset="100%" stop-color="#f59e0b" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <polygon points="{{ $areaCoords }}" fill="url(#saasChartFill)"/>
                            <polyline points="{{ $coords }}" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            @foreach ($points->values() as $i => $p)
                                @php
                                    $cx = $padX + ($count > 1 ? ($i / ($count - 1)) * $innerW : $innerW / 2);
                                    $cy = $padY + $innerH - (($p['total'] / $maxChart) * $innerH);
                                @endphp
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="4" fill="#f59e0b" stroke="#14171f" stroke-width="2"/>
                            @endforeach
                        </svg>
                    </div>
                    <div class="saas-chart-stats">
                        <div>
                            <div class="saas-chart-stat__num">{{ $totalItems }}</div>
                            <div class="saas-chart-stat__lbl">Total items</div>
                            <div class="saas-chart-stat__chg saas-chart-stat__chg--up">↑ Active</div>
                        </div>
                        <div>
                            <div class="saas-chart-stat__num">{{ $kpis['projects'] ?? 0 }}</div>
                            <div class="saas-chart-stat__lbl">Projects</div>
                            <div class="saas-chart-stat__chg saas-chart-stat__chg--up">↑ Portfolio</div>
                        </div>
                        <div>
                            <div class="saas-chart-stat__num">{{ $kpis['case_studies'] ?? 0 }}</div>
                            <div class="saas-chart-stat__lbl">Case studies</div>
                            <div class="saas-chart-stat__chg saas-chart-stat__chg--up">↑ Content</div>
                        </div>
                        <div>
                            <div class="saas-chart-stat__num">{{ $contentHealth['active_blocks'] ?? 0 }}/3</div>
                            <div class="saas-chart-stat__lbl">Blocks live</div>
                            <div class="saas-chart-stat__chg {{ $readiness >= 80 ? 'saas-chart-stat__chg--up' : 'saas-chart-stat__chg--down' }}">{{ $readiness >= 80 ? '↑' : '↓' }} Health</div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Activity panel --}}
            <aside class="saas-widget saas-grid__4">
                <div class="saas-widget__head">
                    <div>
                        <h3>Recent activity</h3>
                        <p>Latest CMS changes</p>
                    </div>
                    <a href="{{ url('/admin/portfolio-projects') }}" class="saas-widget__link">View all</a>
                </div>
                <div class="saas-widget__body saas-widget__body--flush">
                    <div class="saas-activity">
                        @forelse ($recent as $row)
                            @php
                                $iconClass = match ($row['type']) {
                                    'Project' => 'saas-metric__icon--orange',
                                    'Service' => 'saas-metric__icon--blue',
                                    'Insight' => 'saas-metric__icon--green',
                                    default => 'saas-metric__icon--amber',
                                };
                            @endphp
                            <div class="saas-activity__item">
                                <span class="saas-activity__icon saas-metric__icon {{ $iconClass }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </span>
                                <div class="saas-activity__body">
                                    <strong>{{ $row['type'] }}: {{ Str::limit($row['title'], 28) }}</strong>
                                    <span>{{ $row['when'] }}</span>
                                </div>
                                <span class="saas-activity__chevron">›</span>
                            </div>
                        @empty
                            <div class="saas-activity__item">
                                <div class="saas-activity__body">
                                    <strong>No recent edits</strong>
                                    <span>Start by adding content</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>

            <footer class="saas-workspace__footer saas-grid__12">
                <span>© {{ date('Y') }} {{ $branding['app_name'] }}. All rights reserved.</span>
                <span>
                    <a href="{{ $links['site'] }}" target="_blank" rel="noopener">Live site</a>
                    <a href="{{ $links['site_api'] }}" target="_blank" rel="noopener">API</a>
                </span>
            </footer>
        </div>
    </div>
</x-filament-panels::page>
