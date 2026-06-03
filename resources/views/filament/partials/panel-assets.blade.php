{{-- Theme CSS is loaded once via AdminPanelProvider::viteTheme(metronic.css). --}}
@vite([
    'resources/css/filament/workspace-shell.css',
    'resources/css/filament/resource-editor-forms.css',
    'resources/js/filament/metronic.js',
    'resources/js/filament/workspace-shell.js',
])

@include('filament.partials.auth-flash')
@include('filament.partials.swal-flash')

@if (auth()->check())
    <script type="application/json" id="auth-user-avatar-url">@json(\Filament\Facades\Filament::getUserAvatarUrl(auth()->user()))</script>
@endif
