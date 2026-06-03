@push('styles')
    @vite('resources/css/filament/dashboard.css')
@endpush

@push('scripts')
    @vite('resources/js/filament/portfolio-alerts.js')
@endpush

<x-filament-panels::page class="saas-list-records-page">
    <div class="saas-list-stats-wrap">
        @include('filament.partials.list-stats-cards', ['stats' => $this->listStats])
    </div>

    {{ $this->table }}
</x-filament-panels::page>
