{{-- Manager / Admin Dashboard Redesign --}}

<style>
    .mgr-hero {
        display: grid;
        grid-template-columns: 60% 40%;
        gap: 28px;
        align-items: center;
        padding: 24px 0 32px;
        animation: heroIn 360ms cubic-bezier(0.22, 1, 0.36, 1);
    }
    .mgr-hero-meta {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    .mgr-hero-title {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.025em;
        color: var(--text-primary);
        margin: 0 0 10px;
        line-height: 1.15;
    }
    .mgr-hero-desc {
        font-size: 14.5px;
        color: var(--text-secondary);
        line-height: 1.6;
        margin: 0 0 18px;
        max-width: 520px;
    }

    /* KPI Cards with Delta Comparisons */
    .kpi-grid-mgr {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .kpi-card {
        position: relative;
        overflow: hidden;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }
    .kpi-card .kpi-deco {
        position: absolute;
        right: -20px; top: -20px;
        width: 110px; height: 110px;
        border-radius: 50%;
        opacity: .3;
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
        width: 42px; height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kpi-badge-delta {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 999px;
    }
    .kpi-badge-delta.up { background: #ECFDF5; color: #047857; }
    .kpi-badge-delta.down { background: #FEE2E2; color: #B91C1C; }
    .kpi-badge-delta.neutral { background: #F1F5F9; color: #475569; }

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
    .kpi-card .kpi-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Comparison Analytics & Chart Grid */
    .chart-layout-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 28px;
    }
    .analytics-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 24px;
    }
    .card-title-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 12px;
    }
    .card-title-wrap h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-title-wrap .sub-title {
        font-size: 12.5px;
        color: var(--text-muted);
        font-weight: 400;
        margin-top: 2px;
    }

    /* Chart Legend & Custom Bar Visualization */
    .chart-legend {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 12px;
        font-weight: 600;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-secondary);
    }
    .legend-dot {
        width: 10px; height: 10px;
        border-radius: 3px;
    }

    .chart-bars-container {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        height: 220px;
        padding-top: 24px;
        padding-bottom: 8px;
        border-bottom: 1px dashed var(--border);
        gap: 12px;
    }
    .bar-group-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        justify-content: flex-end;
        position: relative;
    }
    .bars-wrapper {
        display: flex;
        align-items: flex-end;
        gap: 4px;
        width: 100%;
        max-width: 52px;
        justify-content: center;
        height: 180px;
    }
    .single-bar {
        flex: 1;
        max-width: 14px;
        border-radius: 4px 4px 0 0;
        transition: height .3s cubic-bezier(0.22, 1, 0.36, 1), opacity .15s ease;
        position: relative;
    }
    .single-bar:hover {
        opacity: .85;
    }
    .single-bar .bar-tooltip {
        position: absolute;
        bottom: 105%;
        left: 50%;
        transform: translateX(-50%);
        background: #0F172A;
        color: #FFF;
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 6px;
        border-radius: 4px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity .15s ease;
        z-index: 10;
    }
    .single-bar:hover .bar-tooltip {
        opacity: 1;
    }
    .bar-month-label {
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-top: 10px;
        text-align: center;
    }
    .bar-month-label.is-current {
        color: var(--primary-600);
        font-weight: 800;
    }

    /* Comparison Summary Breakdown Card */
    .summary-breakdown-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .summary-item {
        padding: 14px;
        border-radius: 12px;
        background: var(--surface-subtle, #F8FAFC);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .summary-item .info h4 {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 2px;
    }
    .summary-item .info p {
        font-size: 11.5px;
        color: var(--text-muted);
        margin: 0;
    }
    .summary-item .num {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-primary);
        text-align: right;
    }

    @media (min-width: 1920px) {
        .mgr-hero-title { font-size: 34px; }
        .mgr-hero-desc { font-size: 16px; max-width: 600px; }
        .kpi-card { padding: 26px; border-radius: 20px; }
        .kpi-card .kpi-value { font-size: 36px; }
        .kpi-card .kpi-label { font-size: 13px; }
        .kpi-card .kpi-sub { font-size: 13.5px; }
        .analytics-card { padding: 30px; border-radius: 22px; }
        .card-title-wrap h3 { font-size: 18px; }
        .chart-bars-container { height: 260px; }
        .bars-wrapper { height: 210px; max-width: 64px; }
        .single-bar { max-width: 18px; }
        .summary-item { padding: 18px; }
        .summary-item .info h4 { font-size: 15px; }
        .summary-item .num { font-size: 22px; }
    }

    @media (max-width: 1024px) {
        .kpi-grid-mgr { grid-template-columns: repeat(2, 1fr); }
        .chart-layout-grid { grid-template-columns: 1fr; }
        .mgr-hero { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .kpi-grid-mgr { grid-template-columns: 1fr; }
    }
</style>

{{-- Hero Section --}}
<section class="mgr-hero">
    <div>
        <div class="mgr-hero-meta">{{ now()->translatedFormat('l, d F Y') }}</div>
        <h1 class="mgr-hero-title">Dashboard Manager Marketing 👋</h1>
        <p class="mgr-hero-desc">
            Pantau seluruh prospek aktif <strong>sedang diproses oleh semua tim marketing</strong>, evaluasi persentase perbandingan dengan bulan sebelumnya, dan analisis performa closing.
        </p>
        <div class="hero-actions">
            <a href="http://192.168.2.35:8000/prospects" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Lihat Semua Prospek
            </a>
            <a href="{{ route('manager.todos') }}" class="btn btn-ghost"><span class="arrow">→</span> Monitor Todolist Harian</a>
        </div>
    </div>
    <div class="hero-illustration" aria-hidden="true">
        <svg viewBox="0 0 360 200" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="mgr-bg-new" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#EFF6FF"/><stop offset="100%" stop-color="#DBEAFE"/>
                </linearGradient>
            </defs>
            <rect width="360" height="200" rx="16" fill="url(#mgr-bg-new)"/>
            {{-- Dashboard Graphic SVG preview --}}
            <rect x="30" y="30" width="300" height="140" rx="12" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.5"/>
            <rect x="50" y="55" width="70" height="40" rx="8" fill="#EFF6FF"/>
            <rect x="135" y="55" width="70" height="40" rx="8" fill="#ECFDF5"/>
            <rect x="220" y="55" width="70" height="40" rx="8" fill="#FEF2F2"/>
            {{-- Chart lines mockup --}}
            <path d="M 50 140 Q 100 110, 150 125 T 250 85 T 290 70" fill="none" stroke="#2563EB" stroke-width="3" stroke-linecap="round"/>
            <path d="M 50 145 Q 100 135, 150 140 T 250 120 T 290 105" fill="none" stroke="#059669" stroke-width="2.5" stroke-dasharray="4 4" stroke-linecap="round"/>
        </svg>
    </div>
</section>

{{-- KPI Grid with Month-over-Month % Comparisons --}}
<section class="kpi-grid-mgr">
    {{-- KPI 1: Prospek Bulan Ini vs Bulan Lalu --}}
    <div class="kpi-card">
        <span class="kpi-deco" style="background: radial-gradient(circle, var(--primary-200), transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: var(--primary-50); color: var(--primary-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
            </div>
            @php
                $pClass = $percentageProspek > 0 ? 'up' : ($percentageProspek < 0 ? 'down' : 'neutral');
                $pIcon = $percentageProspek > 0 ? '↑' : ($percentageProspek < 0 ? '↓' : '•');
            @endphp
            <span class="kpi-badge-delta {{ $pClass }}">
                {{ $pIcon }} {{ abs($percentageProspek) }}% vs bln lalu
            </span>
        </div>
        <div class="kpi-label">Prospek Masuk (Bulan Ini)</div>
        <div class="kpi-value" style="color: var(--primary-600);">{{ $totalProspekMonth ?? 0 }}</div>
        <div class="kpi-sub">
            <span>Bulan lalu: <strong>{{ $totalProspekPrevMonth ?? 0 }}</strong> prospek</span>
            <span style="margin-left:auto; color:{{ $diffProspek >= 0 ? '#047857' : '#B91C1C' }}; font-weight:700;">
                {{ $diffProspek >= 0 ? '+'.$diffProspek : $diffProspek }}
            </span>
        </div>
    </div>

    {{-- KPI 2: Closing Bulan Ini vs Bulan Lalu --}}
    <div class="kpi-card">
        <span class="kpi-deco" style="background: radial-gradient(circle, #A7F3D0, transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: #ECFDF5; color: #047857;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            @php
                $cClass = $percentageClosing > 0 ? 'up' : ($percentageClosing < 0 ? 'down' : 'neutral');
                $cIcon = $percentageClosing > 0 ? '↑' : ($percentageClosing < 0 ? '↓' : '•');
            @endphp
            <span class="kpi-badge-delta {{ $cClass }}">
                {{ $cIcon }} {{ abs($percentageClosing) }}% vs bln lalu
            </span>
        </div>
        <div class="kpi-label">Prospek Closing</div>
        <div class="kpi-value" style="color: #047857;">{{ $closingMonth ?? 0 }}</div>
        <div class="kpi-sub">
            <span>Bulan lalu: <strong>{{ $closingPrevMonth ?? 0 }}</strong> closing</span>
        </div>
    </div>

    {{-- KPI 3: Cancel Bulan Ini vs Bulan Lalu --}}
    <div class="kpi-card">
        <span class="kpi-deco" style="background: radial-gradient(circle, #FECACA, transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: #FEF2F2; color: #DC2626;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            @php
                // Untuk cancel, kenaikan adalah indikator merah (down/warning), penurunan indikator hijau (up)
                $cnClass = $percentageCancel > 0 ? 'down' : ($percentageCancel < 0 ? 'up' : 'neutral');
                $cnIcon = $percentageCancel > 0 ? '↑' : ($percentageCancel < 0 ? '↓' : '•');
            @endphp
            <span class="kpi-badge-delta {{ $cnClass }}">
                {{ $cnIcon }} {{ abs($percentageCancel) }}% vs bln lalu
            </span>
        </div>
        <div class="kpi-label">Prospek Cancel</div>
        <div class="kpi-value" style="color: #DC2626;">{{ $cancelMonth ?? 0 }}</div>
        <div class="kpi-sub">
            <span>Bulan lalu: <strong>{{ $cancelPrevMonth ?? 0 }}</strong> cancel</span>
        </div>
    </div>

    {{-- KPI 4: Total Nominal Closing --}}
    <div class="kpi-card">
        <span class="kpi-deco" style="background: radial-gradient(circle, #FDE68A, transparent 70%);"></span>
        <div class="kpi-head">
            <div class="kpi-icon" style="background: #FEF3C7; color: #B45309;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <span class="kpi-badge-delta neutral">Rp Nominal</span>
        </div>
        <div class="kpi-label">Nominal Closing</div>
        <div class="kpi-value" style="color: #B45309;">
            Rp {{ number_format(($nominalMonth ?? 0) / 1000000, 1, ',', '.') }}<span style="font-size:14px; font-weight:700; color:var(--text-muted); margin-left:3px;">Jt</span>
        </div>
        <div class="kpi-sub">
            <span>Total: Rp {{ number_format($nominalMonth ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>
</section>

{{-- Chart & Comparison Section --}}
<section class="chart-layout-grid">
    {{-- Bar Chart Grafik Perbandingan 6 Bulan --}}
    <div class="analytics-card">
        <div class="card-title-wrap">
            <div>
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary-600);"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                    Grafik Perbandingan Performa 6 Bulan Terakhir
                </h3>
                <div class="sub-title">Perbandingan tren Total Prospek, Closing, dan Cancel tiap bulan</div>
            </div>
            <div class="chart-legend">
                <span class="legend-item"><span class="legend-dot" style="background:#2563EB;"></span> Total Prospek</span>
                <span class="legend-item"><span class="legend-dot" style="background:#059669;"></span> Closing</span>
                <span class="legend-item"><span class="legend-dot" style="background:#DC2626;"></span> Cancel</span>
            </div>
        </div>

        @php
            $maxVal = 1;
            foreach ($monthlyComparison as $m) {
                if ($m['total'] > $maxVal) $maxVal = $m['total'];
            }
        @endphp

        <div class="chart-bars-container">
            @foreach ($monthlyComparison as $m)
                @php
                    $hTotal = max(6, round(($m['total'] / $maxVal) * 160));
                    $hClosing = max(6, round(($m['closing'] / $maxVal) * 160));
                    $hCancel = max(6, round(($m['cancel'] / $maxVal) * 160));
                @endphp
                <div class="bar-group-col">
                    <div class="bars-wrapper">
                        {{-- Bar Total Prospek --}}
                        <div class="single-bar" style="height: {{ $hTotal }}px; background: #2563EB;">
                            <span class="bar-tooltip">Total: {{ $m['total'] }}</span>
                        </div>
                        {{-- Bar Closing --}}
                        <div class="single-bar" style="height: {{ $hClosing }}px; background: #059669;">
                            <span class="bar-tooltip">Closing: {{ $m['closing'] }}</span>
                        </div>
                        {{-- Bar Cancel --}}
                        <div class="single-bar" style="height: {{ $hCancel }}px; background: #DC2626;">
                            <span class="bar-tooltip">Cancel: {{ $m['cancel'] }}</span>
                        </div>
                    </div>
                    <div class="bar-month-label {{ $m['is_current'] ? 'is-current' : '' }}">
                        {{ $m['short_label'] }}
                        @if ($m['is_current'])
                            <span style="font-size:9px; display:block; color:var(--primary-600);">(Bulan Ini)</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Ringkasan Evaluasi Bulan Ini --}}
    <div class="analytics-card">
        <div class="card-title-wrap">
            <div>
                <h3>Rasio Konversi</h3>
                <div class="sub-title">Performa bulan {{ now()->translatedFormat('F Y') }}</div>
            </div>
        </div>
        <div class="summary-breakdown-list">
            @php
                $crRate = ($totalProspekMonth ?? 0) > 0 ? round((($closingMonth ?? 0) / $totalProspekMonth) * 100, 1) : 0;
                $cnRate = ($totalProspekMonth ?? 0) > 0 ? round((($cancelMonth ?? 0) / $totalProspekMonth) * 100, 1) : 0;
            @endphp
            <div class="summary-item">
                <div class="info">
                    <h4>Conversion Rate (Closing)</h4>
                    <p>Persentase prospek sukses closing</p>
                </div>
                <div class="num" style="color:#059669;">{{ $crRate }}%</div>
            </div>
            <div class="summary-item">
                <div class="info">
                    <h4>Cancellation Rate</h4>
                    <p>Persentase prospek yang batal/cancel</p>
                </div>
                <div class="num" style="color:#DC2626;">{{ $cnRate }}%</div>
            </div>
            <div class="summary-item">
                <div class="info">
                    <h4>Marketing Aktif</h4>
                    <p>Jumlah anggota tim marketing</p>
                </div>
                <div class="num" style="color:#2563EB;">{{ $activeMarketings ?? 0 }} Orang</div>
            </div>
        </div>
    </div>
</section>

{{-- Kepatuhan To-Do Hari Ini --}}
<section class="analytics-card" style="padding: 0; overflow: hidden; margin-bottom: 28px;">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary-600);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Kepatuhan To-Do Hari Ini ({{ today()->translatedFormat('d F Y') }})
            </h3>
            <p style="font-size: 12.5px; color: var(--text-muted); margin: 4px 0 0;">
                Monitoring 7 tugas harian: 12 link aktivitas (T1), upload PDF (T2-T6), dan update catatan progres prospek (T7).
            </p>
        </div>
        <a href="{{ route('manager.todos') }}" class="btn btn-sm btn-ghost">
            Lihat Detail Rekap & Kunci To-Do →
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #F8FAFC; border-bottom: 1px solid var(--border);">
                    <th style="padding: 12px 20px; text-align: left; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary);">Marketing</th>
                    <th style="padding: 12px 20px; text-align: left; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary);">Status Hari Ini</th>
                    <th style="padding: 12px 20px; text-align: center; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary);">Total Selesai</th>
                    <th style="padding: 12px 20px; text-align: center; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary);">T1 (12 Links)</th>
                    <th style="padding: 12px 20px; text-align: center; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary);">T2-T6 (PDFs)</th>
                    <th style="padding: 12px 20px; text-align: left; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary);">T7 (Catatan Prospek)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($todoComplianceToday ?? [] as $row)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px 20px; font-weight: 600; color: var(--text-primary);">
                            {{ $row['name'] }}
                        </td>
                        <td style="padding: 14px 20px;">
                            @if ($row['is_empty'])
                                <span class="badge" style="background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; font-weight:700; padding:3px 9px; border-radius:999px; font-size:11px;">Belum Input</span>
                            @elseif ($row['is_complete'])
                                <span class="badge" style="background:#ECFDF5; color:#047857; border:1px solid #A7F3D0; font-weight:700; padding:3px 9px; border-radius:999px; font-size:11px;">Selesai Lengkap (7/7)</span>
                            @else
                                <span class="badge" style="background:#FEF3C7; color:#B45309; border:1px solid #FDE68A; font-weight:700; padding:3px 9px; border-radius:999px; font-size:11px;">Sedang Mengisi</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; text-align: center; font-weight: 800;">
                            <span style="display:inline-block; padding:3px 10px; border-radius:8px; background:{{ $row['tasks_done'] >= 7 ? '#ECFDF5' : ($row['tasks_done'] > 0 ? '#FEF3C7' : '#F1F5F9') }}; color:{{ $row['tasks_done'] >= 7 ? '#047857' : ($row['tasks_done'] > 0 ? '#B45309' : '#64748B') }};">
                                {{ $row['tasks_done'] }} / 7
                            </span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; font-weight: 700;">
                            <span style="color: {{ $row['links_count'] >= 12 ? '#047857' : ($row['links_count'] > 0 ? '#B45309' : 'var(--text-muted)') }};">
                                {{ $row['links_count'] }}/12
                            </span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; font-weight: 700;">
                            <span style="color: {{ $row['pdfs_count'] > 0 ? '#047857' : 'var(--text-muted)' }};">
                                {{ $row['pdfs_count'] }} File
                            </span>
                        </td>
                        <td style="padding: 14px 20px; max-width: 260px;">
                            @if ($row['has_note'])
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span style="color:#047857; font-weight:700; font-size:11px; background:#ECFDF5; padding:2px 6px; border-radius:4px; flex-shrink:0;">✓ Terisi</span>
                                    <span style="font-size:11.5px; color:var(--text-secondary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['note_text'] }}">
                                        {{ Str::limit($row['note_text'], 32) }}
                                    </span>
                                </div>
                            @else
                                <span style="color:var(--text-muted); font-size:11.5px;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">Belum ada data marketing.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
