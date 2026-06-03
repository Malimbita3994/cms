@php
    $homeUrl = filament()->getUrl();
    $brandName = filament()->getBrandName() ?? 'SAP CMS';
    $subtitle = \App\Models\SiteSetting::query()->value('app_name') ?? 'System Analyst Portfolio';
@endphp
<a href="{{ $homeUrl }}" class="saas-topbar-brand" aria-label="{{ $brandName }} home">
    <img
        src="{{ asset('loggoo.png') }}"
        alt=""
        class="saas-topbar-brand__mark"
        width="28"
        height="28"
    />
    <span class="saas-topbar-brand__text">
        <strong>{{ $brandName }}</strong>
        <span>{{ $subtitle }}</span>
    </span>
</a>
