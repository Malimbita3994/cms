@props([
    'stats' => [],
])

@if (count($stats) > 0)
    <section class="saas-analytics saas-analytics--list saas-analytics--enhanced" aria-label="Summary statistics">
        @foreach ($stats as $stat)
            @php
                $color = $stat['color'] ?? 'orange';
                $share = isset($stat['share']) ? (int) $stat['share'] : null;
            @endphp
            <article class="saas-metric saas-metric--{{ $color }}">
                <div class="saas-metric__accent" aria-hidden="true"></div>
                <div class="saas-metric__glow" aria-hidden="true"></div>

                <div class="saas-metric__head">
                    <span class="saas-metric__icon saas-metric__icon--{{ $color }}">
                        @switch($stat['icon'] ?? 'chart')
                            @case('users')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                @break
                            @case('shield')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                @break
                            @case('spark')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/></svg>
                                @break
                            @case('active')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m22 4-10 10.01-3-3"/></svg>
                                @break
                            @case('inactive')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 14.14 14.14"/></svg>
                                @break
                            @case('skill')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></svg>
                                @break
                            @case('image')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                @break
                            @case('star')
                                <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5"><path d="M12 2l2.9 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 7.1-1.01L12 2z"/></svg>
                                @break
                            @case('clock')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                @break
                            @case('wrench')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                @break
                            @case('folder')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7.5V6a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1.5"/><path d="M3 7.5h18"/></svg>
                                @break
                            @default
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 16l4-8 4 5 5-7"/></svg>
                        @endswitch
                    </span>

                    <div class="saas-metric__head-end">
                        @if ($share !== null && ($stat['show_share'] ?? true))
                            <span class="saas-metric__badge">{{ $share }}%</span>
                        @endif
                        @if (! empty($stat['url']))
                            <a href="{{ $stat['url'] }}" class="saas-metric__arrow" aria-label="{{ $stat['label'] }}">›</a>
                        @endif
                    </div>
                </div>

                <div class="saas-metric__body">
                    <p class="saas-metric__eyebrow">{{ $stat['label'] }}</p>
                    <p class="saas-metric__value">{{ $stat['value'] }}</p>
                    @if (! empty($stat['hint']))
                        <p class="saas-metric__hint">{{ $stat['hint'] }}</p>
                    @endif
                </div>

                @if ($share !== null && $share > 0 && ($stat['show_track'] ?? true))
                    <div class="saas-metric__track" role="presentation">
                        <span class="saas-metric__fill" style="width: {{ min(100, max(0, $share)) }}%"></span>
                    </div>
                @endif
            </article>
        @endforeach
    </section>
@endif
