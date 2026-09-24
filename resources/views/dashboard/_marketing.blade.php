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

    /* ====== To Do redesigned ====== */
    .todo-card {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .todo-head {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .todo-head .todo-ic {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .todo-head .todo-ic.ok { background: #ECFDF5; color: #047857; }
    .todo-head .todo-ic.empty { background: #FEE2E2; color: #B91C1C; }
    .todo-head .todo-ic.partial { background: #FEF3C7; color: #B45309; }
    .todo-head .todo-meta { flex: 1; min-width: 0; }
    .todo-head .todo-meta .lbl {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-secondary);
    }
    .todo-head .todo-meta .ttl {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .todo-head .todo-meta .ttl .pill {
        font-size: 10.5px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .todo-head .todo-meta .ttl .pill.ok { background: #ECFDF5; color: #047857; }
    .todo-head .todo-meta .ttl .pill.empty { background: #FEE2E2; color: #B91C1C; }
    .todo-head .todo-meta .ttl .pill.partial { background: #FEF3C7; color: #B45309; }

    .todo-progress {
        background: #F1F5F9;
        border-radius: 8px;
        height: 8px;
        overflow: hidden;
        position: relative;
    }
    .todo-progress .bar {
        height: 100%;
        background: linear-gradient(90deg, #3B82F6, #2563EB);
        border-radius: 8px;
        transition: width .6s cubic-bezier(.22,1,.36,1);
    }
    .todo-progress .bar.ok { background: linear-gradient(90deg, #10B981, #059669); }
    .todo-progress .bar.partial { background: linear-gradient(90deg, #F59E0B, #D97706); }

    .todo-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .todo-stat {
        background: #FAFBFC;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .todo-stat .si {
        width: 30px; height: 30px;
        border-radius: 8px;
        background: var(--primary-50);
        color: var(--primary-600);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .todo-stat .si.pdf {
        background: #FEF3C7;
        color: #B45309;
    }
    .todo-stat .v {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
    }
    .todo-stat .k {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .todo-cta {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .todo-cta .btn { padding: 9px 14px; font-size: 13px; }

    @media (max-width: 720px) {
        .todo-stats { grid-template-columns: 1fr 1fr; }
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
    $linksCount = $todayTodo?->links?->count() ?? 0;
    $pdfsCount  = $todayTodo?->pdfs?->count() ?? 0;
    $pdfTaskKeys = [2,3,4,5,6];
    $pdfTasksFilled = $todayTodo ? $todayTodo->pdfs->groupBy('task')->keys()->intersect($pdfTaskKeys)->count() : 0;
    $targetLinks = 12;
    $targetTasks = 6; // 1 link task + 5 PDF tasks
    $linkTaskDone = $linksCount >= $targetLinks ? 1 : 0;
    $tasksDone = $linkTaskDone + $pdfTasksFilled;
    $pct = $todayTodo ? min(100, round(($tasksDone / $targetTasks) * 100)) : 0;
    $allDone = $tasksDone === $targetTasks;
    if ($todayTodo && $allDone) {
        $todoState = 'ok';
        $stateLabel = 'Lengkap';
    } elseif ($todayTodo && $tasksDone > 0) {
        $todoState = 'partial';
        $stateLabel = 'Sebagian';
    } elseif ($todayTodo) {
        $todoState = 'partial';
        $stateLabel = 'Mulai';
    } else {
        $todoState = 'empty';
        $stateLabel = 'Belum Input';
    }
@endphp

<section class="kpi-grid">
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

    {{-- To Do Hari Ini — REDESIGNED --}}
    <a href="{{ route('todos.daily') }}" class="card card-hover kpi-card todo-card" style="text-decoration:none; color:inherit; display:flex;">
        <span class="kpi-deco" style="background: radial-gradient(circle, #FDE68A, transparent 70%);"></span>
        <div class="todo-head">
            <div class="todo-ic {{ $todoState }}">
                @if($todoState === 'ok')
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                @elseif($todoState === 'partial')
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                @else
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                @endif
            </div>
            <div class="todo-meta">
                <div class="lbl">To Do Hari Ini</div>
                <div class="ttl">
                    {{ $todoState === 'empty' ? 'Belum ada input' : ($todoState === 'ok' ? 'Siap dijalankan' : 'Sedang berjalan') }}
                    <span class="pill {{ $todoState }}">{{ $stateLabel }}</span>
                </div>
            </div>
        </div>

        <div class="todo-progress">
            <div class="bar {{ $todoState }}" style="width: {{ $pct }}%;"></div>
        </div>

        <div class="todo-stats">
            <div class="todo-stat">
                <span class="si">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                </span>
                <div>
                    <div class="v">{{ $tasksDone }}</div>
                    <div class="k">/ {{ $targetTasks }} Tugas</div>
                </div>
            </div>
            <div class="todo-stat">
                <span class="si pdf">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </span>
                <div>
                    <div class="v">{{ $linksCount }}</div>
                    <div class="k">/ {{ $targetLinks }} Link</div>
                </div>
            </div>
        </div>

        <div class="todo-cta">
            <span class="btn btn-primary" style="pointer-events:none; background: var(--primary-500); color:#fff;">
                @if($todoState === 'empty')
                    Input Sekarang
                @elseif($todoState === 'ok')
                    Lihat Detail
                @else
                    Lanjutkan
                @endif
                <span class="arrow">→</span>
            </span>
            <span style="font-size:12px; color:var(--text-muted);">{{ now()->format('d M Y') }}</span>
        </div>
    </a>
</section>

{{-- Status breakdown cards --}}
<section class="kpi-grid" style="margin-top: 16px;">
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

    {{-- Prospek Cancel --}}
    <a href="{{ route('prospects.index') }}" class="card card-hover kpi-card" style="text-decoration:none; color:inherit; display:block;">
        <span class="kpi-deco" style="background: radial-gradient(circle, #FCA5A5, transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: #FEE2E2; color: #B91C1C;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <span class="kpi-spark down">Batal</span>
        </div>
        <div class="kpi-label">Total Prospek Cancel</div>
        <div class="kpi-value" style="color: #B91C1C;">{{ $prospekCancel ?? 0 }}</div>
        <div class="kpi-meta">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Prospek dibatalkan
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