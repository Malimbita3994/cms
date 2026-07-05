<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Support\CmsAuth::DOCUMENT_TITLE }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            --bg: #120c09;
            --surface: #1c120d;
            --border: rgba(255, 255, 255, 0.1);
            --text: #fafafa;
            --muted: #a1a1aa;
            --accent: #f3500f;
            --accent-hot: #fb8f10;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --danger-text: #fca5a5;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            position: relative;
            margin: 0;
            min-height: 100%;
            box-sizing: border-box;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
            color: var(--text);
            background: #09090b;
            -webkit-font-smoothing: antialiased;
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

        .card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            padding: 32px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            background: linear-gradient(125.1deg, #232323 28.16%, #2f241f 120.37%);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.45);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent-hot), var(--accent));
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .brand-name {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .brand-sub {
            margin: 2px 0 0;
            font-size: 0.85rem;
            color: var(--muted);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.5rem;
            letter-spacing: -0.03em;
        }

        .lead {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .alert {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 10px;
            background: var(--danger-bg);
            color: var(--danger-text);
            font-size: 0.9rem;
        }

        .alert p { margin: 0; }
        .alert p + p { margin-top: 6px; }

        .field { display: block; margin-bottom: 16px; }

        .field span {
            display: block;
            margin-bottom: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--muted);
        }

        .field input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #0f0a07;
            color: var(--text);
            font: inherit;
        }

        .field input:focus {
            outline: none;
            border-color: rgba(243, 80, 15, 0.55);
            box-shadow: 0 0 0 3px rgba(243, 80, 15, 0.15);
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 0.88rem;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            cursor: pointer;
        }

        .forgot {
            color: var(--accent-hot);
            text-decoration: none;
            font-weight: 600;
        }

        .forgot:hover { text-decoration: underline; }

        .submit {
            width: 100%;
            padding: 13px 16px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-hot), var(--accent));
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        .submit:hover { filter: brightness(1.05); }
    </style>
</head>
<body>
    <div class="login-bg" aria-hidden="true"></div>
    <div class="login-overlay" aria-hidden="true"></div>
    <main class="card">
        <div class="brand">
            @if (file_exists(public_path('loggoo.png')))
                <img src="{{ asset('loggoo.png') }}" alt="" class="brand-logo" width="42" height="42">
            @else
                <span class="brand-mark" aria-hidden="true">B</span>
            @endif
            <div>
                <p class="brand-name">{{ \App\Support\CmsAuth::BRAND_LABEL }}</p>
                <p class="brand-sub">Admin only</p>
            </div>
        </div>

        <h1>Sign in</h1>

        @if ($errors->any())
            <div class="alert" role="alert">
                @foreach ($errors->all() as $message)
                    <p>{{ $message }}</p>
                @endforeach
            </div>
        @endif

        <form method="post" action="{{ route('welcome.login') }}">
            @csrf

            <label class="field">
                <span>Email</span>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="username"
                    required
                    autofocus
                />
            </label>

            <label class="field">
                <span>Password</span>
                <input
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    required
                />
            </label>

            <div class="row">
                <label class="remember">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Remember me</span>
                </label>
                <a class="forgot" href="{{ \App\Support\CmsAuth::passwordResetUrl() }}">Forgot password?</a>
            </div>

            <button type="submit" class="submit">Sign in</button>
        </form>
    </main>
</body>
</html>
