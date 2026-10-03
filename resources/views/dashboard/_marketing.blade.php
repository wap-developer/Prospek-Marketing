{{-- Marketing dashboard: hero + 3 KPI cards + prospek table --}}

<style>
    /* ====== KPI redesign ====== */
    .kpi-card {
        position: relative;
        overflow: hidden;
    }
    .kpi-card .kpi-deco {
        position: absolute;
        right: -20px;
        top: -20px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        opacity: .35;
        filter: blur(2px);
        pointer-events: none;
    }
    .kpi-card .kpi-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .kpi-card .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kpi-card .kpi-spark {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 999px;
        background: #ECFDF5;
        color: #047857;
    }
    .kpi-card .kpi-spark.down {
        background: #FEE2E2;
        color: #B91C1C;
    }
    .kpi-card .kpi-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    .kpi-card .kpi-value {
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1;
        color: var(--text-primary);
    }
    .kpi-card .kpi-meta {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .kpi-card .kpi-meta-action {
        color: var(--primary-600);
        font-weight: 700;
    }

    /* ====== To Do Card Compact Redesign ====== */
    .todo-compact-card {
        position: relative;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        text-decoration: none;
        color: inherit;
        box-shadow: var(--shadow-card);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .todo-compact-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        border-color: var(--primary-300);
    }
    .todo-compact-card.ok { border-left: 4px solid #10B981; }
    .todo-compact-card.partial { border-left: 4px solid #F59E0B; }
    .todo-compact-card.empty { border-left: 4px solid #3B82F6; }

    .todo-compact-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .todo-head-main {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .todo-mini-ic {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .todo-mini-ic.ok { background: #ECFDF5; color: #047857; }
    .todo-mini-ic.partial { background: #FEF3C7; color: #B45309; }
    .todo-mini-ic.empty { background: #EFF6FF; color: #2563EB; }

    .todo-title-box {
        display: flex;
        align-items: baseline;
        gap: 8px;
        min-width: 0;
        flex-wrap: wrap;
    }
    .todo-compact-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }
    .todo-compact-date {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .todo-badge-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    .todo-compact-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 999px;
        text-transform: capitalize;
        line-height: 1.2;
    }
    .todo-compact-badge .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .todo-compact-badge.ok { background: #ECFDF5; color: #047857; }
    .todo-compact-badge.ok .dot { background: #10B981; }
    .todo-compact-badge.partial { background: #FEF3C7; color: #B45309; }
    .todo-compact-badge.partial .dot { background: #F59E0B; }
    .todo-compact-badge.empty { background: #FEE2E2; color: #B91C1C; }
    .todo-compact-badge.empty .dot { background: #EF4444; }

    .todo-arrow-ic {
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        transition: transform .15s ease, color .15s ease;
    }
    .todo-compact-card:hover .todo-arrow-ic {
        color: var(--primary-600);
        transform: translateX(2px);
    }

    .todo-progress-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .todo-bar-bg {
        flex: 1;
        height: 6px;
        background: #F1F5F9;
        border-radius: 999px;
        overflow: hidden;
    }
    .todo-bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: width .5s ease;
    }
    .todo-bar-fill.ok { background: linear-gradient(90deg, #10B981, #059669); }
    .todo-bar-fill.partial { background: linear-gradient(90deg, #F59E0B, #D97706); }
    .todo-bar-fill.empty { background: #CBD5E1; }
    .todo-bar-pct {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-secondary);
        flex-shrink: 0;
        min-width: 32px;
        text-align: right;
    }

    .todo-compact-chips {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        background: #F8FAFC;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 5px 0;
    }
    .todo-chip {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2px 4px;
        text-align: center;
    }
    .todo-chip:not(:last-child) {
        border-right: 1px solid var(--border);
    }
    .todo-chip .val {
        font-weight: 800;
        color: var(--text-primary);
        font-size: 13px;
        line-height: 1.1;
    }
    .todo-chip .denom {
        font-size: 10.5px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .todo-chip .lbl {
        color: var(--text-secondary);
        font-size: 10px;
        font-weight: 600;
        margin-top: 2px;
        white-space: nowrap;
    }

    .todo-btn-desktop {
        display: none;
    }

    /* 4-Column KPI Grid for Prospect Cards */
    .kpi-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (min-width: 900px) {
        .todo-compact-card {
            padding: 14px 20px;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }
        .todo-compact-head {
            flex: 1;
            min-width: 0;
            justify-content: flex-start;
        }
        .todo-progress-row {
            width: 170px;
            flex-shrink: 0;
        }
        .todo-compact-chips {
            width: 300px;
            flex-shrink: 0;
            padding: 6px 0;
        }
        .todo-arrow-ic {
            display: none;
        }
        .todo-btn-desktop {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            background: var(--primary-500);
            color: #fff;
            box-shadow: 0 2px 5px rgba(37,99,235,0.2);
            flex-shrink: 0;
            transition: background .15s ease;
        }
        .todo-btn-desktop.ok {
            background: #059669;
            box-shadow: 0 2px 5px rgba(5,150,105,0.2);
        }
        .todo-compact-card:hover .todo-btn-desktop {
            background: var(--primary-600);
        }
        .todo-compact-card:hover .todo-btn-desktop.ok {
            background: #047857;
        }
    }

    @media (max-width: 1080px) {
        .kpi-grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .kpi-grid-4 {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
    }
</style>

<section class="hero">
    <div class="hero-text">
        <div class="hero-greet">Good Day! ☀️</div>
        <h1 class="hero-title">Halo, {{ $user->name }} <span aria-hidden="true">👋</span></h1>
        <p class="hero-desc">Selamat datang di <strong>HIVEFIVE Prospect System</strong>.<br>Kelola prospek, selesaikan tugas harian, dan capai target lebih mudah!</p>
        <div class="hero-actions">
            <a href="{{ route('todos.daily') }}" class="btn btn-primary">Input To Do Hari Ini</a>
            <a href="{{ route('prospects.index') }}" class="btn btn-ghost"><span class="arrow">→</span> Lihat Prospek Saya</a>
        </div>
    </div>
    <div class="hero-illustration" aria-hidden="true">
        <svg viewBox="0 0 320 220" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#DBEAFE"/>
                    <stop offset="100%" stop-color="#EFF6FF"/>
                </linearGradient>
            </defs>
            <circle cx="160" cy="120" r="100" fill="url(#bg)"/>
            <rect x="80" y="110" width="160" height="90" rx="6" fill="#2563EB"/>
            <rect x="88" y="116" width="144" height="74" rx="3" fill="#FFFFFF"/>
            <rect x="100" y="128" width="80" height="6" rx="3" fill="#BFDBFE"/>
            <rect x="100" y="142" width="120" height="4" rx="2" fill="#DBEAFE"/>
            <rect x="100" y="152" width="100" height="4" rx="2" fill="#DBEAFE"/>
            <rect x="100" y="162" width="60"  height="4" rx="2" fill="#DBEAFE"/>
            <rect x="60"  y="198" width="200" height="8" rx="4" fill="#1D4ED8"/>
            <rect x="180" y="148" width="8" height="20" rx="2" fill="#10B981"/>
            <rect x="192" y="138" width="8" height="30" rx="2" fill="#10B981"/>
            <rect x="204" y="128" width="8" height="40" rx="2" fill="#2563EB"/>
            <circle cx="60" cy="60" r="14" fill="#FACC15" opacity=".75"/>
            <rect x="244" y="44" width="22" height="22" rx="5" fill="#10B981" opacity=".75" transform="rotate(15 255 55)"/>
            <path d="M250 80 L260 75 L255 90 Z" fill="#2563EB" opacity=".5"/>
            <path d="M44 160 Q 70 140 90 150" stroke="#1D4ED8" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <path d="M88 146 L94 150 L88 154" stroke="#1D4ED8" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</section>

@php
    $stats = $todayTodoStats ?? null;
    if ($stats) {
        $linksCount = $stats['links_count'];
        $targetLinks = $stats['target_links'];
        $pdfTasksFilled = $stats['pdf_tasks_filled'];
        $targetPdfTasks = $stats['target_pdf_tasks'];
        $tasksDone = $stats['tasks_done'];
        $targetTasks = $stats['total_tasks'];
        $pct = $stats['pct'];
        $todoState = $stats['state'];
        $stateLabel = $stats['state_label'];
    } else {
        $linksCount = $todayTodo?->links?->count() ?? 0;
        $targetLinks = 12;
        $linkTaskDone = $linksCount >= $targetLinks ? 1 : 0;
        $pdfTaskKeys = [2,3,4,5,6];
        $pdfTasksFilled = $todayTodo ? $todayTodo->pdfs->groupBy('task')->keys()->intersect($pdfTaskKeys)->count() : 0;
        $targetPdfTasks = 5;
        $task7Done = !empty(trim((string)($todayTodo?->prospect_progress_note ?? ''))) ? 1 : 0;
        $tasksDone = $linkTaskDone + $pdfTasksFilled + $task7Done;
        $targetTasks = 7;
        $pct = min(100, round(($tasksDone / $targetTasks) * 100));
        $allDone = $tasksDone >= $targetTasks;
        $todoState = $allDone ? 'ok' : ($tasksDone > 0 ? 'partial' : 'empty');
        $stateLabel = $allDone ? 'Lengkap' : ($tasksDone > 0 ? 'Sebagian' : 'Belum Input');
    }
@endphp

{{-- To Do Hari Ini Compact Card --}}
<a href="{{ route('todos.daily') }}" class="card card-hover todo-compact-card {{ $todoState }}">
    <div class="todo-compact-head">
        <div class="todo-head-main">
            <span class="todo-mini-ic {{ $todoState }}">
                @if($todoState === 'ok')
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                @elseif($todoState === 'partial')
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                @else
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                @endif
            </span>
            <div class="todo-title-box">
                <span class="todo-compact-title">To Do Hari Ini</span>
                <span class="todo-compact-date">{{ now()->translatedFormat('d M Y') }}</span>
            </div>
        </div>
        <div class="todo-badge-wrap">
            <span class="todo-compact-badge {{ $todoState }}">
                <span class="dot"></span>
                <span>{{ $stateLabel }}</span>
            </span>
            <span class="todo-arrow-ic" aria-hidden="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
        </div>
    </div>

    <div class="todo-progress-row">
        <div class="todo-bar-bg">
            <div class="todo-bar-fill {{ $todoState }}" style="width: {{ $pct }}%;"></div>
        </div>
        <span class="todo-bar-pct">{{ $pct }}%</span>
    </div>

    <div class="todo-compact-chips">
        <div class="todo-chip">
            <span class="val">{{ $tasksDone }}<span class="denom">/{{ $targetTasks }}</span></span>
            <span class="lbl">Tugas Selesai</span>
        </div>
        <div class="todo-chip">
            <span class="val">{{ $linksCount }}<span class="denom">/{{ $targetLinks }}</span></span>
            <span class="lbl">Link Medsos</span>
        </div>
        <div class="todo-chip">
            <span class="val">{{ $pdfTasksFilled }}<span class="denom">/{{ $targetPdfTasks }}</span></span>
            <span class="lbl">Upload PDF</span>
        </div>
    </div>

    <span class="todo-btn-desktop {{ $todoState }}">
        @if($todoState === 'empty')
            Input Sekarang
        @elseif($todoState === 'ok')
            Lihat Detail
        @else
            Lanjutkan
        @endif
        <span class="arrow">→</span>
    </span>
</a>

{{-- 4 Kartu KPI Prospek --}}
<section class="kpi-grid-4">
    {{-- Prospek Hari Ini --}}
    <a href="{{ route('prospects.index') }}" class="card card-hover kpi-card" style="text-decoration:none; color:inherit; display:block;">
        <span class="kpi-deco" style="background: radial-gradient(circle, var(--primary-200), transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: var(--primary-50); color: var(--primary-600);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
            </div>
            <span class="kpi-spark">Hari Ini</span>
        </div>
        <div class="kpi-label">Prospek Hari Ini</div>
        <div class="kpi-value" style="color: var(--primary-600);">{{ $prospekToday ?? 0 }}</div>
        <div class="kpi-meta">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ now()->format('d M Y') }}
        </div>
    </a>

    {{-- Prospek Bulan Ini --}}
    <a href="{{ route('prospects.index') }}" class="card card-hover kpi-card" style="text-decoration:none; color:inherit; display:block;">
        <span class="kpi-deco" style="background: radial-gradient(circle, #A7F3D0, transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: #ECFDF5; color: #047857;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 4 4 5-6"/></svg>
            </div>
            <span class="kpi-spark">{{ now()->format('M Y') }}</span>
        </div>
        <div class="kpi-label">Prospek Bulan Ini</div>
        <div class="kpi-value" style="color: #047857;">{{ $prospekMonth ?? 0 }}</div>
        <div class="kpi-meta">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            Akumulasi bulan berjalan
        </div>
    </a>

    {{-- Prospek Open --}}
    <a href="{{ route('prospects.index') }}" class="card card-hover kpi-card" style="text-decoration:none; color:inherit; display:block;">
        <span class="kpi-deco" style="background: radial-gradient(circle, var(--primary-200), transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: var(--primary-50); color: var(--primary-600);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span class="kpi-spark" style="background:#EFF6FF; color:#1D4ED8;">Aktif</span>
        </div>
        <div class="kpi-label">Total Prospek Open</div>
        <div class="kpi-value" style="color: var(--primary-600);">{{ $prospekOpen ?? 0 }}</div>
        <div class="kpi-meta">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Sedang berjalan
        </div>
    </a>

    {{-- Prospek Closed (Closing = deal) --}}
    <a href="{{ route('prospects.index') }}" class="card card-hover kpi-card" style="text-decoration:none; color:inherit; display:block;">
        <span class="kpi-deco" style="background: radial-gradient(circle, #A7F3D0, transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: #ECFDF5; color: #047857;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <span class="kpi-spark">Deal</span>
        </div>
        <div class="kpi-label">Total Prospek Closed</div>
        <div class="kpi-value" style="color: #047857;">{{ $prospekClosing ?? 0 }}</div>
        <div class="kpi-meta">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Deal berhasil
        </div>
    </a>
</section>

<section class="prospect-section">
    <div class="section-header">
        <div>
            <div class="section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <h2 class="section-title">Prospek Terbaru Saya</h2>
            <p class="section-desc">Berikut adalah prospek terbaru yang telah ditambahkan.</p>
        </div>
        <a href="{{ route('prospects.index') }}" class="btn btn-ghost"><span class="arrow">→</span> Lihat semua</a>
    </div>

    <div class="card" style="padding: 0; overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Client</th>
                    <th>Layanan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($myProspek ?? [] as $p)
                    <tr>
                        <td>{{ $p->entry_date->format('d/m/Y') }} <span style="color:var(--text-muted); font-size:11.5px;">{{ $p->entry_time }}</span></td>
                        <td>{{ $p->client_phone }}</td>
                        <td>{{ $p->service->name }}</td>
                        <td><span class="badge badge-{{ $p->status->slug }}">{{ $p->status->name }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center; color: var(--text-muted); padding: 32px;">Belum ada prospek. <a href="{{ route('prospects.index') }}">Lihat semua →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>