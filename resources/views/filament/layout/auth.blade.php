@php
    use App\Support\AuthSidebarStats;

    $livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
    $sidebar = AuthSidebarStats::cached();
    $brand = $sidebar['brand'];
    $stats = $sidebar['stats'];

    $statCards = [
        ['value' => $stats['projects'], 'label' => 'Projects', 'icon' => 'projects'],
        ['value' => $stats['services'], 'label' => 'Services', 'icon' => 'services'],
        ['value' => $stats['case_studies'], 'label' => 'Case studies', 'icon' => 'case-studies'],
        ['value' => $stats['insights'], 'label' => 'Insights', 'icon' => 'insights'],
    ];
@endphp

@push('styles')
    @vite('resources/css/filament/auth.css')
@endpush

@push('scripts')
    @vite('resources/js/filament/portfolio-alerts.js')
    @include('filament.partials.auth-flash')
@endpush

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="mb-auth-shell dark">
        <aside class="mb-auth-brand" aria-label="Portfolio CMS overview">
            <div class="mb-auth-brand-glow" aria-hidden="true"></div>
            <div class="mb-auth-brand-inner">
                <a href="{{ url('/') }}" class="mb-auth-logo">
                    <span class="mb-auth-mark">{{ strtoupper(substr($brand, 0, 1)) }}</span>
                    <span>{{ $brand }}</span>
                </a>

                <p class="mb-auth-tag">Portfolio CMS</p>
                <h2 class="mb-auth-title">Manage your public site from one secure control panel.</h2>
                <p class="mb-auth-lead">
                    Publish projects, services, insights, and case studies. Your Next.js frontend stays in sync through the REST API.
                </p>

                <div class="mb-auth-stat-grid" role="list">
                    @foreach ($statCards as $card)
                        <article class="mb-auth-stat-card mb-auth-stat-card--{{ $card['icon'] }}" role="listitem">
                            <div class="mb-auth-stat-icon" aria-hidden="true">
                                @if ($card['icon'] === 'projects')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7.5V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v1.5"/><path d="M3 7.5h18v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-12z"/><path d="M8 11h8M8 15h5"/></svg>
                                @elseif ($card['icon'] === 'services')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/><path d="M12 12l8-4.5M12 12v9M12 12L4 7.5"/></svg>
                                @elseif ($card['icon'] === 'case-studies')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path d="M14 3v6h6"/><path d="M9 13h6M9 17h4"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v13A2.5 2.5 0 0 1 17.5 21h-11A2.5 2.5 0 0 1 4 18.5v-13z"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                                @endif
                            </div>
                            <div class="mb-auth-stat-body">
                                <span class="mb-auth-stat-value">{{ $card['value'] }}</span>
                                <span class="mb-auth-stat-label">{{ $card['label'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <p class="mb-auth-foot">Laravel {{ app()->version() }} · Filament admin</p>
            </div>
        </aside>

        <main class="mb-auth-main">
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}
            <div class="mb-auth-form-wrap">
                {{ $slot }}
            </div>
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
            <p class="mb-auth-back">
                <a href="{{ url('/') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to login
                </a>
            </p>
        </main>
    </div>

    <style>
        .mb-auth-shell .fi-input-wrp,
        .mb-auth-shell .fi-input-wrp:focus-within {
            box-shadow: none !important;
            --tw-shadow: 0 0 #0000 !important;
            --tw-ring-shadow: 0 0 #0000 !important;
            --tw-inset-shadow: 0 0 #0000 !important;
            --tw-inset-ring-shadow: 0 0 #0000 !important;
        }
        .mb-auth-shell .fi-input-wrp input.fi-input {
            border: 0 !important;
            outline: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .mb-auth-actions-row {
            display: grid !important;
            grid-template-columns: 1fr auto !important;
            align-items: center !important;
        }
    </style>
</x-filament-panels::layout.base>
