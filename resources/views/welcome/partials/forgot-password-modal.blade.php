@php
    $forgotErrors = $errors->getBag('forgot_password');
    $hasForgotErrors = $forgotErrors->any();
    $statusMessage = session('forgot_password_status');
@endphp

<div
    id="welcome-forgot-modal"
    class="login-modal"
    @if (! $openForgotModal) hidden @endif
    aria-hidden="{{ $openForgotModal ? 'false' : 'true' }}"
>
    <div class="login-modal__backdrop" data-forgot-close tabindex="-1" aria-hidden="true"></div>

    <div
        class="login-modal__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="forgot-modal-title"
        aria-describedby="forgot-modal-desc"
    >
        <button type="button" class="login-modal__close" data-forgot-close aria-label="Close password reset">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>

        <button type="button" class="login-modal__back-btn" data-login-open>
            <span aria-hidden="true">←</span>
            Back to login
        </button>

        <div class="login-modal__brand">
            <span class="brand-mark" aria-hidden="true">{{ strtoupper(substr($appName, 0, 1)) }}</span>
            <div>
                <p class="login-modal__eyebrow">Admin access</p>
                <h2 id="forgot-modal-title" class="login-modal__title">Forgot password?</h2>
            </div>
        </div>

        <p id="forgot-modal-desc" class="login-modal__lead">
            Enter your email and we will send you a link to choose a new password.
        </p>

        @if ($statusMessage)
            <div class="login-modal__alert login-modal__alert--success" role="status">
                <p>{{ $statusMessage }}</p>
            </div>
        @endif

        @if ($hasForgotErrors)
            <div class="login-modal__alert" role="alert">
                @foreach ($forgotErrors->all() as $message)
                    <p>{{ $message }}</p>
                @endforeach
            </div>
        @endif

        @if (! $statusMessage)
            <form method="post" action="{{ route('welcome.password-reset') }}" class="login-modal__form">
                @csrf

                <label class="login-field">
                    <span class="login-field__label">Email address</span>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@example.local"
                        autocomplete="username"
                        required
                        autofocus
                    />
                </label>

                <button type="submit" class="btn btn-primary login-modal__submit">
                    Send email
                </button>
            </form>
        @else
            <button type="button" class="btn btn-primary login-modal__submit" data-login-open>
                Back to login
            </button>
        @endif

        <p class="login-modal__foot">
            Prefer the full page?
            <a href="{{ url('/admin/password-reset/request') }}">Open classic reset</a>
        </p>
    </div>
</div>
