@if (filament()->hasPasswordReset())
    <a href="{{ filament()->getRequestPasswordResetUrl() }}" class="mb-auth-forgot" tabindex="-1">
        Forgot password?
        <span class="mb-auth-forgot-chevron" aria-hidden="true">›</span>
    </a>
@endif
