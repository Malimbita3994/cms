@props([
    'stats' => [],
])

@if (count($stats) > 0)
    <section class="posters-kpi" aria-label="Summary statistics">
        @foreach ($stats as $stat)
            @php
                $color = $stat['color'] ?? 'blue';
            @endphp
            <article class="posters-kpi__card posters-kpi__card--{{ $color }}">
                <span class="posters-kpi__icon posters-kpi__icon--{{ $color }}" aria-hidden="true">
                    @switch($stat['icon'] ?? 'chart')
                        @case('spark')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/></svg>
                            @break
                        @case('star')
                            <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5"><path d="M12 2l2.9 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 7.1-1.01L12 2z"/></svg>
                            @break
                        @case('clock')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            @break
                        @default
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 16l4-8 4 5 5-7"/></svg>
                    @endswitch
                </span>
                <div class="posters-kpi__copy">
                    <p class="posters-kpi__label">{{ $stat['label'] }}</p>
                    <p class="posters-kpi__value">{{ $stat['value'] }}</p>
                </div>
                @if (! empty($stat['trend']))
                    <span class="posters-kpi__trend posters-kpi__trend--{{ $color }}">{{ $stat['trend'] }}</span>
                @endif
            </article>
        @endforeach
    </section>
@endif
