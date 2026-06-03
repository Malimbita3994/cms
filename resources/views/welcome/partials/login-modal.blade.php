@php
    $loginErrors = $errors->getBag('default');
    $hasErrors = $loginErrors->any();
@endphp

<div
    id="welcome-login-modal"
    class="login-modal"
    @if (! $openLoginModal) hidden @endif
    aria-hidden="{{ $openLoginModal ? 'false' : 'true' }}"
>
    <div class="login-modal__backdrop" data-login-close tabindex="-1" aria-hidden="true"></div>

    <div
        class="login-modal__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="login-modal-title"
        aria-describedby="login-modal-desc"
    >
        <button type="button" class="login-modal__close" data-login-close aria-label="Close sign in">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>

        <div class="login-modal__brand">
            <span class="brand-mark" aria-hidden="true">{{ strtoupper(substr($appName, 0, 1)) }}</span>
            <div>
                <p class="login-modal__eyebrow">Admin access</p>
                <h2 id="login-modal-title" class="login-modal__title">Welcome back</h2>
            </div>
        </div>

        <p id="login-modal-desc" class="login-modal__lead">
            Sign in to manage your portfolio content, projects, and site settings.
        </p>

        @if ($hasErrors)
            <div class="login-modal__alert" role="alert">
                @foreach ($loginErrors->all() as $message)
                    <p>{{ $message }}</p>
                @endforeach
            </div>
        @endif

        <form method="post" action="{{ route('welcome.login') }}" class="login-modal__form">
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

            <label class="login-field">
                <span class="login-field__label">Password</span>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                />
            </label>

            <div class="login-modal__row">
                <label class="login-remember">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember')) />
                    <span>Remember me</span>
                </label>
                <button type="button" class="login-modal__forgot" data-forgot-open>Forgot password?</button>
            </div>

            <button type="submit" class="btn btn-primary login-modal__submit">
                Sign in to admin
            </button>
        </form>

        <p class="login-modal__foot">
            Prefer the full page?
            <a href="{{ url('/admin/login') }}">Open classic login</a>
        </p>
    </div>
</div>
