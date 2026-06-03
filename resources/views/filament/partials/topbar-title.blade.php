@unless (request()->routeIs('filament.admin.pages.dashboard'))
    @php
        $component = \Livewire\Livewire::current();
        $title = null;

        if ($component && method_exists($component, 'getTitle')) {
            $resolved = $component->getTitle();
            $title = is_string($resolved) && $resolved !== '' ? $resolved : null;
        }

        if (blank($title) && $component && method_exists($component, 'getHeading')) {
            $heading = $component->getHeading();
            $title = is_string($heading) && $heading !== '' ? $heading : null;
        }
    @endphp
    @if (filled($title))
        <div class="saas-topbar-page">
            <h1 class="saas-topbar-title">{{ $title }}</h1>
        </div>
    @endif
@endunless
