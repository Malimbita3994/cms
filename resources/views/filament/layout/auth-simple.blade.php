@php
    $livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

@push('styles')
    @vite('resources/css/filament/auth.css')
@endpush

@push('scripts')
    @vite('resources/js/filament/portfolio-alerts.js')
    @include('filament.partials.auth-flash')
@endpush

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="login-bg" aria-hidden="true"></div>
    <div class="login-overlay" aria-hidden="true"></div>
    <div class="mb-auth-shell mb-auth-shell--simple dark">
        <main class="mb-auth-main mb-auth-main--simple">
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
        body.fi-body.fi-panel-admin {
            background: #09090b !important;
            overflow: hidden;
        }

        html.fi-panel-admin,
        html:has(body.fi-panel-admin) {
            height: 100%;
            overflow: hidden;
        }

        .login-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: url("{{ asset('Home.jpg') }}") center center / cover no-repeat;
            transform-origin: center;
            will-change: transform;
            animation: login-bg-drift 12s ease-in-out infinite alternate;
        }

        .login-overlay {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background:
                linear-gradient(213.44deg, rgba(0, 0, 0, 0.88) 43.78%, rgba(186, 72, 17, 0.55) 124.39%),
                radial-gradient(circle at top right, rgba(251, 143, 16, 0.2), transparent 55%);
        }

        @keyframes login-bg-drift {
            from { transform: scale(1) translate(0, 0); }
            to { transform: scale(1.08) translate(24px, -18px); }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-bg { animation: none; }
        }

        .mb-auth-shell--simple {
            position: relative;
            z-index: 2;
            grid-template-columns: 1fr !important;
            place-items: center;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            box-sizing: border-box;
            padding: 1.5rem;
            background: transparent;
        }

        .mb-auth-main--simple {
            width: 100%;
            max-width: 28rem;
            min-height: auto;
            padding: 0;
            background: transparent;
        }

        .mb-auth-main--simple::before {
            display: none;
        }

        .mb-auth-shell--simple .fi-simple-layout,
        .mb-auth-shell--simple .fi-simple-main-ctn,
        .mb-auth-shell--simple .fi-simple-main {
            background: transparent !important;
        }

        .mb-auth-shell--simple .fi-simple-page-content {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            background: linear-gradient(125.1deg, #232323 28.16%, #2f241f 120.37%);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.45);
        }

        .mb-auth-shell--simple .fi-input-wrp,
        .mb-auth-shell--simple .fi-input-wrp:focus-within {
            box-shadow: none !important;
            --tw-shadow: 0 0 #0000 !important;
            --tw-ring-shadow: 0 0 #0000 !important;
            --tw-inset-shadow: 0 0 #0000 !important;
            --tw-inset-ring-shadow: 0 0 #0000 !important;
        }

        .mb-auth-shell--simple .fi-input-wrp input.fi-input {
            border: 0 !important;
            outline: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
    </style>
</x-filament-panels::layout.base>
