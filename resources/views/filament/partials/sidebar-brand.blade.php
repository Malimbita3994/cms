@php
    $subtitle = \App\Models\SiteSetting::query()->value('app_name') ?? 'System Analyst Portfolio';
@endphp
<p class="saas-brand-subtitle">{{ $subtitle }}</p>
