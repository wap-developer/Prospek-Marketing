{{-- CS dashboard --}}
<section class="hero">
    <div class="hero-text">
        <div class="hero-greet">Halo, CS! 👋</div>
        <h1 class="hero-title">{{ auth()->user()->name }}</h1>
        <p class="hero-desc">Input prospek baru dari klien dan kirim ke tim marketing yang tepat.</p>
        <div class="hero-actions">
            @if (isset($prospectLockSetting) && $prospectLockSetting->is_locked)
                <button type="button" class="btn" style="background: #FFFFFF; color: #991B1B; border: 1.5px solid #FECACA; box-shadow: 0 4px 12px rgba(153, 27, 27, 0.08); display: inline-flex; align-items: center; gap: 8px; font-weight: 700; cursor: pointer;" onclick="alert('Input prospek sedang dikunci. Jam operasional pembuatan prospek baru adalah pukul 06:00 - 22:00 WIB.')">
                    <span style="width: 20px; height: 20px; border-radius: 5px; background: #FEE2E2; color: #DC2626; display: inline-flex; align-items: center; justify-content: center;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <span>Input Prospek Dikunci</span>
                    <span style="font-size: 11px; font-weight: 700; color: #B91C1C; background: #FEE2E2; padding: 2px 7px; border-radius: 6px;">22:00 - 06:00</span>
                </button>
            @else
                <a href="{{ route('prospects.create') }}" class="btn btn-primary">+ Input Prospek Baru</a>
            @endif
            <a href="{{ route('prospects.index') }}" class="btn btn-ghost"><span class="arrow">→</span> Lihat Semua Prospek</a>
        </div>
        @if (isset($prospectLockSetting) && $prospectLockSetting->is_locked)
            <div style="margin-top: 14px; padding: 10px 14px; background: #FEE2E2; border: 1px solid #FCA5A5; border-radius: 10px; color: #991B1B; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>{{ $prospectLockSetting->reason ?: 'Input prospek ditutup pukul 22:00 - 06:00 WIB. Pembuatan prospek baru dinonaktifkan.' }}</span>
            </div>
        @endif
    </div>
    <div class="hero-illustration" aria-hidden="true">
        <svg viewBox="0 0 320 220" xmlns="http://www.w3.org/2000/svg">
            <defs><linearGradient id="bg3" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#DBEAFE"/><stop offset="100%" stop-color="#EFF6FF"/></linearGradient></defs>
            <circle cx="160" cy="120" r="100" fill="url(#bg3)"/>
            <rect x="100" y="60" width="120" height="140" rx="10" fill="#FFFFFF" stroke="#E2E8F0"/>
            <rect x="115" y="80" width="90" height="8" rx="3" fill="#2563EB"/>
            <rect x="115" y="96" width="60" height="6" rx="3" fill="#BFDBFE"/>
            <rect x="115" y="116" width="90" height="38" rx="4" fill="#EFF6FF"/>
            <rect x="120" y="124" width="50" height="5" rx="2" fill="#BFDBFE"/>
            <rect x="120" y="134" width="70" height="5" rx="2" fill="#DBEAFE"/>
            <rect x="115" y="160" width="90" height="6" rx="3" fill="#1D4ED8"/>
            <circle cx="240" cy="80" r="14" fill="#10B981"/>
            <path d="M233 80 l5 5 l9 -10" stroke="#FFFFFF" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="60" cy="170" r="12" fill="#FACC15" opacity=".75"/>
        </svg>
    </div>
</section>

<section class="kpi-grid kpi-grid-2">
    <div class="card card-hover kpi-card">
        <div class="kpi-icon" style="background: var(--primary-50); color: var(--primary-600);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="22" height="22"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
        </div>
        <div class="kpi-label">Input Hari Ini</div>
        <div class="kpi-value" style="color: var(--primary-600);">{{ $myCreatedToday ?? 0 }}</div>
        <div class="kpi-meta">Prospek kamu hari ini</div>
    </div>

    <div class="card card-hover kpi-card">
        <div class="kpi-icon" style="background: #FEF3C7; color: #B45309;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="22" height="22"><path d="M3 3v18h18"/><path d="M7 14l4-4 4 4 5-6"/></svg>
        </div>
        <div class="kpi-label">Input Bulan Ini</div>
        <div class="kpi-value" style="color: #B45309;">{{ $myCreatedMonth ?? 0 }}</div>
        <div class="kpi-meta">Akumulasi bulan berjalan</div>
    </div>
</section>
