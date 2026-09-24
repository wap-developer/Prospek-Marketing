<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — HIVEFIVE Prospek System</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/hiv.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/hiv.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --navy: #172554;
            --slate: #64748B;
            --border: #D7E0EA;
            --surface: #FFFFFF;
            --bg: #F8FAFC;
            --soft-blue: #EFF6FF;
            --soft-cyan: #ECFEFF;
            --hive-yellow: #FACC15;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--navy);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: var(--primary);
        }

        /* ============== BACKGROUND ============== */
        .bg-decor {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 12% 35%, rgba(59, 130, 246, 0.18), transparent 28%),
                radial-gradient(circle at 88% 65%, rgba(56, 189, 248, 0.16), transparent 26%),
                #f8fafc;
        }

        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(to right, rgba(37, 99, 235, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(37, 99, 235, 0.05) 1px, transparent 1px);
            background-size: 44px 44px;
            -webkit-mask-image: radial-gradient(circle at 50% 50%, black 35%, transparent 78%);
            mask-image: radial-gradient(circle at 50% 50%, black 35%, transparent 78%);
        }

        .bg-arc {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(37, 99, 235, 0.06);
        }

        .bg-arc.a1 {
            width: 680px;
            height: 680px;
            top: -200px;
            left: -240px;
        }

        .bg-arc.a2 {
            width: 520px;
            height: 520px;
            bottom: -180px;
            right: -180px;
        }

        .bg-dots {
            position: absolute;
            width: 140px;
            height: 140px;
            background-image: radial-gradient(rgba(37, 99, 235, 0.18) 1.5px, transparent 1.5px);
            background-size: 14px 14px;
        }

        .bg-dots.d1 {
            top: 18%;
            left: 46%;
        }

        .bg-dots.d2 {
            bottom: 16%;
            right: 8%;
        }

        /* ============== LAYOUT ============== */
        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .shell {
            flex: 1;
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 48px 32px 24px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
            align-items: center;
        }

        /* ============== MARKETING PANEL ============== */
        .marketing {
            opacity: 0;
            transform: translateX(-16px);
            animation: fadeX 600ms cubic-bezier(0.22, 1, 0.36, 1) 100ms forwards;
        }

        .brand-logo {
            display: inline-block;
            margin-bottom: 28px;
        }

        .brand-logo img {
            width: 165px;
            height: auto;
            filter: drop-shadow(0 6px 14px rgba(15, 23, 42, 0.06));
        }

        .hero {
            font-size: 48px;
            line-height: 1.08;
            letter-spacing: -0.03em;
            font-weight: 800;
            color: var(--navy);
            margin: 0 0 18px;
        }

        .hero .accent {
            color: var(--primary);
        }

        .hero-sub {
            font-size: 17px;
            line-height: 1.6;
            color: var(--slate);
            max-width: 500px;
            margin: 0 0 36px;
        }

        .features {
            display: grid;
            gap: 18px;
            grid-template-columns: 1fr;
            margin-bottom: 28px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .feature .ico {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature .ico svg {
            width: 22px;
            height: 22px;
        }

        .feature.t .ico {
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary);
        }

        .feature.m .ico {
            background: rgba(56, 189, 248, 0.12);
            color: #0284C7;
        }

        .feature.a .ico {
            background: rgba(250, 204, 21, 0.16);
            color: #B45309;
        }

        .feature .title {
            font-size: 15px;
            font-weight: 700;
            color: var(--navy);
            margin: 0;
            line-height: 1.2;
        }

        .feature .desc {
            font-size: 13px;
            color: var(--slate);
            margin: 2px 0 0;
        }

        .quote-card {
            position: relative;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 18px;
            padding: 22px 22px 22px 50px;
            max-width: 500px;
        }

        .quote-card::before {
            content: "\201C";
            position: absolute;
            left: 18px;
            top: 6px;
            font-size: 40px;
            line-height: 1;
            color: var(--primary);
            font-weight: 700;
        }

        .quote-card p {
            margin: 0;
            font-size: 14.5px;
            color: var(--navy);
            font-style: italic;
            line-height: 1.55;
            border-left: 2px solid var(--primary);
            padding-left: 14px;
        }

        /* ============== LOGIN CARD ============== */
        .login-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            opacity: 0;
            transform: translateY(16px);
            animation: fadeY 600ms cubic-bezier(0.22, 1, 0.36, 1) 250ms forwards;
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 24px 70px rgba(30, 64, 175, 0.10);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .login-brand {
            display: flex;
            justify-content: center;
            margin-bottom: 22px;
        }

        .login-brand img {
            width: 200px;
            height: auto;
        }

        .login-title {
            font-size: 30px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.02em;
            margin: 0 0 6px;
            text-align: center;
        }

        .login-sub {
            font-size: 14px;
            color: var(--slate);
            text-align: center;
            margin: 0 0 28px;
        }

        /* Alerts */
        .alert {
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .alert-success {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .alert-danger {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .alert ul {
            margin: 0;
            padding-left: 18px;
        }

        /* Fields */
        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .lead-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 16px;
            pointer-events: none;
        }

        .input {
            width: 100%;
            height: 50px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
            padding: 0 44px 0 42px;
            font-size: 15px;
            font-family: inherit;
            color: var(--navy);
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .input::placeholder {
            color: #94A3B8;
        }

        .input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .input.is-invalid {
            border-color: #DC2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.10);
        }

        .field-error {
            color: #B91C1C;
            font-size: 12.5px;
            margin-top: 6px;
        }

        .pass-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: 0;
            color: var(--slate);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 8px;
            transition: background .15s ease, color .15s ease;
        }

        .pass-toggle:hover {
            background: var(--soft-blue);
            color: var(--primary);
        }

        .pass-toggle svg {
            width: 18px;
            height: 18px;
        }

        /* Remember row */
        .row-aux {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 18px 0 22px;
            font-size: 13.5px;
        }

        .check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--slate);
            cursor: pointer;
            user-select: none;
        }

        .check input {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1.5px solid #CBD5E1;
            border-radius: 5px;
            background: #fff;
            display: inline-grid;
            place-content: center;
            cursor: pointer;
            transition: all .15s ease;
        }

        .check input:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        .check input:checked::after {
            content: "";
            width: 10px;
            height: 6px;
            border-left: 2px solid #fff;
            border-bottom: 2px solid #fff;
            transform: rotate(-45deg) translate(1px, -1px);
        }

        .check input:focus-visible {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.20);
        }

        .admin-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .admin-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Submit */
        .btn-primary {
            width: 100%;
            height: 52px;
            border: 0;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.22);
            transition: background .15s ease, transform .15s ease, box-shadow .15s ease;
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.30);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.22);
        }

        .btn-primary:disabled {
            opacity: .75;
            cursor: not-allowed;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.18);
        }

        .btn-primary .arrow {
            transition: transform .2s ease;
        }

        .btn-primary:hover:not(:disabled) .arrow {
            transform: translateX(4px);
        }

        .spinner {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-top-color: #fff;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Security notice */
        .security {
            margin-top: 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #F1F6FF;
            border-radius: 14px;
            padding: 14px 16px;
        }

        .security .ico {
            color: var(--primary);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .security .ico svg {
            width: 16px;
            height: 16px;
        }

        .security .t {
            font-size: 13px;
            font-weight: 700;
            color: var(--navy);
            margin: 0;
            line-height: 1.2;
        }

        .security .d {
            font-size: 12px;
            color: var(--slate);
            margin: 2px 0 0;
        }

        /* Footer */
        .page-footer {
            text-align: center;
            font-size: 12.5px;
            color: var(--slate);
            padding: 18px 16px 24px;
        }

        /* Animations */
        @keyframes fadeY {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeX {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive */
        @media (min-width: 900px) {
            .shell {
                grid-template-columns: 48% 52%;
                gap: 80px;
                padding: 64px 40px 32px;
            }

            .features {
                grid-template-columns: 1fr 1fr 1fr;
            }

            .hero {
                font-size: 50px;
            }
        }

        /* ============== 2K / ULTRA-WIDE SCREEN SCALING (1440p / 2560px+) ============== */
        @media (min-width: 1600px) {
            .shell {
                max-width: 1560px;
                gap: 100px;
                padding: 80px 48px 40px;
            }

            .login-card {
                max-width: 520px;
                padding: 48px;
                border-radius: 28px;
            }

            .hero {
                font-size: 58px;
            }

            .hero-sub {
                font-size: 19px;
                max-width: 580px;
            }

            .input {
                height: 56px;
                font-size: 16px;
            }

            .btn-primary {
                height: 58px;
                font-size: 16.5px;
            }

            .login-title {
                font-size: 34px;
            }

            .feature .title {
                font-size: 16px;
            }

            .feature .desc {
                font-size: 14px;
            }
        }

        @media (min-width: 2200px) {
            .shell {
                max-width: 1840px;
                gap: 130px;
                padding: 100px 60px 48px;
            }

            .login-card {
                max-width: 580px;
                padding: 56px;
                border-radius: 32px;
            }

            .hero {
                font-size: 66px;
            }

            .hero-sub {
                font-size: 21px;
                max-width: 680px;
            }

            .input {
                height: 60px;
                font-size: 17px;
            }

            .btn-primary {
                height: 62px;
                font-size: 18px;
            }

            .login-title {
                font-size: 38px;
            }
        }

        @media (max-width: 899px) {
            .marketing {
                display: none;
            }

            .login-card {
                padding: 28px 24px;
                border-radius: 20px;
            }

            .hero {
                font-size: 32px;
            }

            .login-title {
                font-size: 26px;
            }

            .shell {
                padding: 28px 20px 16px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>

    <div class="bg-decor" aria-hidden="true">
        <div class="bg-grid"></div>
        <div class="bg-arc a1"></div>
        <div class="bg-arc a2"></div>
        <div class="bg-dots d1"></div>
        <div class="bg-dots d2"></div>
    </div>

    <div class="page">

        <main class="shell">

            {{-- MARKETING PANEL --}}
            <section class="marketing" aria-label="Tentang HIVEFIVE">
                <a href="{{ route('home') }}" class="brand-logo" aria-label="HIVEFIVE">
                    <img src="{{ asset('assets/logohv.png') }}" alt="HIVEFIVE">
                </a>

                <h1 class="hero">
                    Turn Marketing Efforts<br>
                    Into Measurable<br>
                    <span class="accent">Real Progress</span>
                </h1>

                <p class="hero-sub">
                    Kelola prospek, pantau pipeline, dan wujudkan hasil nyata bersama HiveFive.
                </p>

                <div class="features">
                    <div class="feature t">
                        <div class="ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3v18h18" />
                                <path d="M7 14l4-4 4 4 5-6" />
                            </svg>
                        </div>
                        <div>
                            <p class="title">Track Progress</p>
                            <p class="desc">Pantau performa & KPI real-time.</p>
                        </div>
                    </div>
                    <div class="feature m">
                        <div class="ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <div>
                            <p class="title">Manage Prospect</p>
                            <p class="desc">Pipeline prospek terpusat.</p>
                        </div>
                    </div>
                    <div class="feature a">
                        <div class="ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <circle cx="12" cy="12" r="6" />
                                <circle cx="12" cy="12" r="2" />
                            </svg>
                        </div>
                        <div>
                            <p class="title">Achieve Results</p>
                            <p class="desc">Wujudkan target & pertumbuhan.</p>
                        </div>
                    </div>
                </div>

                <div class="quote-card">
                    <p>Marketing yang terukur, membawa pertumbuhan yang nyata.</p>
                </div>
            </section>

            {{-- LOGIN CARD --}}
            <section class="login-wrap" aria-label="Form login">
                <div class="login-card">

                    <div class="login-brand">
                        <a href="{{ route('home') }}" aria-label="HIVEFIVE">
                            <img src="{{ asset('assets/logohv.png') }}" alt="HIVEFIVE">
                        </a>
                    </div>

                    <h2 class="login-title">Selamat Datang <span aria-hidden="true">👋</span></h2>
                    <p class="login-sub">Masuk untuk mengakses dashboard &amp; pipeline prospek.</p>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            <span>✓</span>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <span>⚠</span>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" novalidate id="loginForm">
                        @csrf

                        <div class="field">
                            <label for="username">Username</label>
                            <div class="input-wrap">
                                <span class="lead-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </span>
                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    value="{{ old('username') }}"
                                    class="input @error('username') is-invalid @enderror"
                                    placeholder="Masukkan username"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                                    aria-describedby="username-error">
                            </div>
                            @error('username')
                                <div id="username-error" class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="password">Password</label>
                            <div class="input-wrap" x-data="{ show: false }">
                                <span class="lead-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    name="password"
                                    :type="show ? 'text' : 'password'"
                                    class="input @error('password') is-invalid @enderror"
                                    placeholder="Masukkan password"
                                    required
                                    autocomplete="current-password">
                                <button type="button" class="pass-toggle"
                                        @click="show = !show"
                                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                                        :aria-pressed="show ? 'true' : 'false'">
                                    <svg x-show="!show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg x-show="show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" style="display:none;">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.51 19.51 0 0 1 4.22-5.39" />
                                        <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a19.6 19.6 0 0 1-3.17 4.19" />
                                        <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row-aux">
                            <label class="check" for="remember">
                                <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                <span>Ingat saya</span>
                            </label>
                            <a href="mailto:admin@prospek.local" class="admin-link" title="Hubungi Super Admin">Hubungi Admin</a>
                        </div>

                        <button type="submit" class="btn-primary">
                            <span class="label">Masuk ke Sistem</span>
                            <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </button>
                    </form>

                    <div class="security" role="note" aria-label="Keamanan akses">
                        <div class="ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <div>
                            <p class="t">Akses Terjamin</p>
                            <p class="d">Data Anda aman bersama kami.</p>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <footer class="page-footer">
            &copy; {{ date('Y') }} Analisa TIM Prospek &middot; HIVEFIVE Prospek System
        </footer>
    </div>

</body>

</html>