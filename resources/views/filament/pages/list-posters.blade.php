@push('styles')
    @vite([
        'resources/css/filament/dashboard.css',
        'resources/css/filament/posters-list.css',
        'resources/css/filament/poster-record-modal.css',
    ])
@endpush

@push('scripts')
    @vite('resources/js/filament/portfolio-alerts.js')
@endpush

<x-filament-panels::page class="posters-list-page saas-list-records-page">
    <div class="saas-list-stats-wrap">
        @include('filament.partials.list-stats-cards', ['stats' => $this->listStats])
    </div>

    {{ $this->table }}
</x-filament-panels::page>
