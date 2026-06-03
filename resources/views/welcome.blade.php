<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $appName }} — secure portfolio content management for your public website.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName }} · Content Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    <style>
        :root {
            --bg: #09090b;
            --surface: #121214;
            --surface-2: #18181b;
            --border: rgba(255, 255, 255, 0.08);
            --text: #fafafa;
            --muted: #a1a1aa;
            --accent: #f3500f;
            --accent-2: #fb8f10;
            --accent-soft: rgba(243, 80, 15, 0.12);
            --success: #22c55e;
            --radius: 14px;
            --shadow: 0 24px 80px rgba(0, 0, 0, 0.45);
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: var(--text);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            border-bottom: 1px solid var(--border);
            background: rgba(9, 9, 11, 0.82);
            backdrop-filter: blur(14px);
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 68px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: -0.02em;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--accent-2), var(--accent));
            display: grid;
            place-items: center;
            font-size: 0.95rem;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 8px 24px rgba(243, 80, 15, 0.35);
        }

        .nav {
            display: none;
            align-items: center;
            gap: 28px;
        }

        .nav a {
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav a:hover { color: var(--text); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 11px;
            padding: 11px 18px;
            font-size: 0.9rem;
            font-weight: 700;
            border: 1px solid var(--border);
            background: var(--surface-2);
            color: var(--text);
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.16);
        }

        .btn-primary {
            border: none;
            color: #fff;
            background: linear-gradient(135deg, var(--accent-2) 0%, var(--accent) 100%);
            box-shadow: 0 12px 32px rgba(243, 80, 15, 0.32);
        }

        .btn-primary:hover {
            filter: brightness(1.05);
        }

        .hero {
            padding: 56px 0 40px;
        }

        .hero-grid {
            display: grid;
            gap: 40px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
        }

        h1 {
            margin: 18px 0 0;
            font-size: clamp(2.1rem, 4.8vw, 3.35rem);
            line-height: 1.08;
            letter-spacing: -0.035em;
            font-weight: 800;
        }

        h1 .gradient {
            background: linear-gradient(135deg, #fff 20%, var(--accent-2) 55%, var(--accent) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .lead {
            margin: 16px 0 0;
            max-width: 540px;
            color: var(--muted);
            font-size: 1.05rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .trust-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .trust-item strong {
            display: block;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .trust-item span {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .preview {
            border: 1px solid var(--border);
            border-radius: calc(var(--radius) + 4px);
            background: linear-gradient(160deg, var(--surface) 0%, #0c0c0e 100%);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .preview-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.25);
        }

        .preview-bar span {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--muted);
        }

        .preview-bar .live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #86efac;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .preview-body {
            display: grid;
            grid-template-columns: 148px 1fr;
            min-height: 320px;
        }

        .preview-nav {
            padding: 14px 10px;
            border-right: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.2);
        }

        .preview-nav a {
            display: block;
            padding: 8px 10px;
            margin-bottom: 4px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--muted);
        }

        .preview-nav a.active {
            color: var(--text);
            background: var(--accent-soft);
            border: 1px solid rgba(243, 80, 15, 0.25);
        }

        .preview-main { padding: 16px; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .stat {
            padding: 14px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--surface-2);
        }

        .stat .value {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        .stat .label {
            margin-top: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .preview-note {
            margin-top: 12px;
            padding: 14px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--surface-2);
            font-size: 0.82rem;
            color: var(--muted);
            line-height: 1.5;
        }

        .section {
            padding: 48px 0;
        }

        .section-head {
            text-align: center;
            max-width: 620px;
            margin: 0 auto 32px;
        }

        .section-head h2 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .section-head p {
            margin: 10px 0 0;
            color: var(--muted);
        }

        .features {
            display: grid;
            gap: 14px;
        }

        .feature {
            padding: 22px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--surface);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: var(--accent-soft);
            color: var(--accent-2);
            font-size: 1.1rem;
            margin-bottom: 14px;
        }

        .feature h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .feature p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .cta-band {
            margin: 20px 0 48px;
            padding: 32px 24px;
            border-radius: calc(var(--radius) + 2px);
            border: 1px solid rgba(243, 80, 15, 0.22);
            background: linear-gradient(135deg, rgba(243, 80, 15, 0.14), rgba(9, 9, 11, 0.9));
            text-align: center;
        }

        .cta-band h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .cta-band p {
            margin: 10px auto 0;
            max-width: 480px;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .cta-band .hero-actions {
            justify-content: center;
            margin-top: 22px;
        }

        .footer {
            padding: 24px 0 36px;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.82rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
        }

        .footer a { color: var(--muted); }
        .footer a:hover { color: var(--text); }

        .nav-cta-mobile { display: inline-flex; }
        .nav .btn-primary { padding: 9px 14px; }

        button.btn {
            cursor: pointer;
            font-family: inherit;
        }

        html.login-modal-open { overflow: hidden; }

        .login-modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .login-modal[hidden] { display: none !important; }

        .login-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.72);
            backdrop-filter: blur(6px);
        }

        .login-modal__dialog {
            position: relative;
            width: min(420px, 100%);
            max-height: min(92vh, 640px);
            overflow-y: auto;
            padding: 28px 24px 24px;
            border-radius: calc(var(--radius) + 2px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(165deg, #161618 0%, #0f0f11 100%);
            box-shadow: 0 32px 90px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(243, 80, 15, 0.12);
        }

        .login-modal__close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 36px;
            height: 36px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface-2);
            color: var(--muted);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: color 0.15s ease, border-color 0.15s ease;
        }

        .login-modal__close:hover {
            color: var(--text);
            border-color: rgba(255, 255, 255, 0.18);
        }

        .login-modal__brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .login-modal__brand .brand-mark {
            width: 42px;
            height: 42px;
            font-size: 1rem;
        }

        .login-modal__eyebrow {
            margin: 0;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent-2);
        }

        .login-modal__title {
            margin: 4px 0 0;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .login-modal__lead {
            margin: 0 0 20px;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .login-modal__alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(239, 68, 68, 0.35);
            background: rgba(239, 68, 68, 0.1);
            color: #fecaca;
            font-size: 0.85rem;
        }

        .login-modal__alert p { margin: 0; }
        .login-modal__alert p + p { margin-top: 6px; }

        .login-modal__form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .login-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .login-field__label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #d4d4d8;
        }

        .login-field input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #0c0c0e;
            color: var(--text);
            font: inherit;
            font-size: 0.92rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .login-field input::placeholder { color: #71717a; }

        .login-field input:focus {
            outline: none;
            border-color: rgba(243, 80, 15, 0.45);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        .login-modal__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .login-remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--muted);
            cursor: pointer;
        }

        .login-remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
        }

        .login-modal__forgot,
        .login-modal__back-btn {
            border: none;
            background: none;
            padding: 0;
            font: inherit;
            cursor: pointer;
        }

        .login-modal__forgot {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--accent-2);
        }

        .login-modal__forgot:hover { color: var(--accent); }

        .login-modal__back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0 0 14px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--accent-2);
        }

        .login-modal__back-btn:hover { color: var(--accent); }

        .login-modal__alert--success {
            border-color: rgba(34, 197, 94, 0.35);
            background: rgba(34, 197, 94, 0.1);
            color: #bbf7d0;
        }

        .login-modal__submit {
            width: 100%;
            margin-top: 4px;
            padding: 13px 18px;
        }

        .login-modal__foot {
            margin: 18px 0 0;
            text-align: center;
            font-size: 0.82rem;
            color: var(--muted);
        }

        .login-modal__foot a {
            color: var(--accent-2);
            font-weight: 600;
        }

        .login-modal__foot a:hover { color: var(--accent); }

        .login-modal__link-btn {
            border: none;
            background: none;
            padding: 0;
            font: inherit;
            color: var(--muted);
            cursor: pointer;
        }

        .login-modal__link-btn:hover { color: var(--text); }

        @media (min-width: 900px) {
            .nav { display: flex; }
            .nav-cta-mobile { display: none; }
            .hero-grid { grid-template-columns: 1.05fr 1fr; gap: 48px; }
            .stat-grid { grid-template-columns: repeat(4, 1fr); }
            .features { grid-template-columns: repeat(3, 1fr); }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="shell topbar-inner">
            <a href="/" class="brand">
                <span class="brand-mark" aria-hidden="true">{{ strtoupper(substr($appName, 0, 1)) }}</span>
                <span>{{ $appName }}</span>
            </a>
            <nav class="nav" aria-label="Primary">
                <a href="#capabilities">Capabilities</a>
                <a href="#security">Security</a>
                <a href="/api/v1/site" target="_blank" rel="noopener">API</a>
                @auth
                    <a href="{{ url('/admin') }}" class="btn btn-primary">Open dashboard</a>
                @else
                    <button type="button" class="btn btn-primary" data-login-open>Sign in</button>
                @endauth
            </nav>
            @guest
                <button type="button" class="btn btn-primary nav-cta-mobile" data-login-open>Sign in</button>
            @else
                <a href="{{ url('/admin') }}" class="btn btn-primary nav-cta-mobile">Dashboard</a>
            @endguest
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="shell hero-grid">
                <div>
                    <span class="eyebrow"><span class="eyebrow-dot"></span> Laravel {{ app()->version() }} · Filament admin</span>
                    <h1>Your portfolio backend,<br><span class="gradient">built for clarity.</span></h1>
                    <p class="lead">
                        Publish projects, services, insights, and case studies from one control panel.
                        Changes sync to your public site through a versioned REST API.
                    </p>
                    <div class="hero-actions">
                        @auth
                            <a href="{{ url('/admin') }}" class="btn btn-primary">Open admin panel</a>
                        @else
                            <button type="button" class="btn btn-primary" data-login-open>Open admin panel</button>
                        @endauth
                        <a href="/api/v1/site" class="btn" target="_blank" rel="noopener">View live API</a>
                    </div>
                    <div class="trust-row">
                        <div class="trust-item">
                            <strong>{{ $stats['projects'] }}</strong>
                            <span>Projects</span>
                        </div>
                        <div class="trust-item">
                            <strong>{{ $stats['services'] }}</strong>
                            <span>Services</span>
                        </div>
                        <div class="trust-item">
                            <strong>{{ $stats['case_studies'] }}</strong>
                            <span>Case studies</span>
                        </div>
                        <div class="trust-item">
                            <strong>{{ $stats['insights'] }}</strong>
                            <span>Insights</span>
                        </div>
                    </div>
                </div>

                <aside class="preview" aria-label="Admin preview">
                    <div class="preview-bar">
                        <span>{{ $appName }} · Admin</span>
                        <span class="live"><span class="eyebrow-dot"></span> Operational</span>
                    </div>
                    <div class="preview-body">
                        <nav class="preview-nav" aria-label="Admin menu preview">
                            <a href="/admin" class="active">Dashboard</a>
                            <a href="/admin/portfolio-projects">Projects</a>
                            <a href="/admin/services">Services</a>
                            <a href="/admin/case-studies">Case studies</a>
                            <a href="/admin/insights">Insights</a>
                            <a href="/admin/profiles">Profile</a>
                            <a href="/admin/site-settings">Site settings</a>
                        </nav>
                        <div class="preview-main">
                            <div class="stat-grid">
                                <div class="stat">
                                    <div class="value">{{ $stats['projects'] }}</div>
                                    <div class="label">Projects</div>
                                </div>
                                <div class="stat">
                                    <div class="value">{{ $stats['services'] }}</div>
                                    <div class="label">Services</div>
                                </div>
                                <div class="stat">
                                    <div class="value">{{ $stats['case_studies'] }}</div>
                                    <div class="label">Case studies</div>
                                </div>
                                <div class="stat">
                                    <div class="value">{{ $stats['insights'] }}</div>
                                    <div class="label">Insights</div>
                                </div>
                            </div>
                            <p class="preview-note">
                                Role-based access, structured content models, and fresh API responses for your Next.js frontend.
                            </p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section id="capabilities" class="section">
            <div class="shell">
                <div class="section-head">
                    <h2>Everything your portfolio needs</h2>
                    <p>Focused tools for editors and developers — without clutter or guesswork.</p>
                </div>
                <div class="features">
                    <article class="feature" id="security">
                        <div class="feature-icon" aria-hidden="true">◆</div>
                        <h3>Secure access</h3>
                        <p>Authenticated Filament panel with roles, permissions, and session protection out of the box.</p>
                    </article>
                    <article class="feature">
                        <div class="feature-icon" aria-hidden="true">◇</div>
                        <h3>Structured content</h3>
                        <p>Typed models for profile, timeline, projects, services, insights, and case studies.</p>
                    </article>
                    <article class="feature">
                        <div class="feature-icon" aria-hidden="true">○</div>
                        <h3>Headless API</h3>
                        <p>Versioned JSON endpoints designed for your public site — no cache surprises during editing.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="shell">
            <div class="cta-band">
                <h2>Ready to manage your content?</h2>
                <p>Sign in to update your portfolio, review publishing health, and keep your public site in sync.</p>
                <div class="hero-actions">
                    @auth
                        <a href="{{ url('/admin') }}" class="btn btn-primary">Go to dashboard</a>
                    @else
                        <button type="button" class="btn btn-primary" data-login-open>Sign in to admin</button>
                    @endauth
                    <a href="/api/v1/site" class="btn" target="_blank" rel="noopener">View live API</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="shell footer">
        <span>© {{ date('Y') }} {{ $appName }}. All rights reserved.</span>
        <span>
            <a href="/api/v1/site">API</a>
            ·
            @guest
                <button type="button" class="login-modal__link-btn" data-login-open>Admin login</button>
            @else
                <a href="{{ url('/admin') }}">Admin dashboard</a>
            @endguest
        </span>
    </footer>

    @guest
        @include('welcome.partials.login-modal')
        @include('welcome.partials.forgot-password-modal')
    @endguest

    <script>
        (function () {
            const loginModal = document.getElementById('welcome-login-modal');
            const forgotModal = document.getElementById('welcome-forgot-modal');
            if (!loginModal && !forgotModal) return;

            const syncBodyLock = () => {
                const anyOpen = [loginModal, forgotModal].some((modal) => modal && !modal.hidden);
                document.documentElement.classList.toggle('login-modal-open', anyOpen);
            };

            const setModal = (modal, open) => {
                if (!modal) return;
                modal.hidden = !open;
                modal.setAttribute('aria-hidden', open ? 'false' : 'true');
            };

            const focusFirstField = (modal) => {
                const field = modal?.querySelector('input:not([type="hidden"])');
                if (field) {
                    setTimeout(() => field.focus(), 50);
                }
            };

            const closeLogin = () => setModal(loginModal, false);
            const closeForgot = () => setModal(forgotModal, false);

            const openLogin = () => {
                closeForgot();
                setModal(loginModal, true);
                syncBodyLock();
                focusFirstField(loginModal);
            };

            const openForgot = () => {
                closeLogin();
                setModal(forgotModal, true);
                syncBodyLock();
                focusFirstField(forgotModal);
            };

            const closeAll = () => {
                closeLogin();
                closeForgot();
                syncBodyLock();
            };

            document.querySelectorAll('[data-login-open]').forEach((el) => {
                el.addEventListener('click', (event) => {
                    event.preventDefault();
                    openLogin();
                });
            });

            document.querySelectorAll('[data-forgot-open]').forEach((el) => {
                el.addEventListener('click', (event) => {
                    event.preventDefault();
                    openForgot();
                });
            });

            loginModal?.querySelectorAll('[data-login-close]').forEach((el) => {
                el.addEventListener('click', () => {
                    closeLogin();
                    syncBodyLock();
                });
            });

            forgotModal?.querySelectorAll('[data-forgot-close]').forEach((el) => {
                el.addEventListener('click', () => {
                    closeForgot();
                    syncBodyLock();
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                const loginOpen = loginModal && !loginModal.hidden;
                const forgotOpen = forgotModal && !forgotModal.hidden;
                if (!loginOpen && !forgotOpen) return;
                closeAll();
            });

            if (loginModal && !loginModal.hidden) {
                openLogin();
            } else if (forgotModal && !forgotModal.hidden) {
                openForgot();
            }
        })();
    </script>
</body>
</html>
