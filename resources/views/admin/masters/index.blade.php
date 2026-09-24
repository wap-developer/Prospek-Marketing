@extends('layouts.app')

@section('title', 'Master Data — Admin')

@section('content')
    <style>
        .masters-page {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* ===== Hero Header ===== */
        .masters-hero {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            border-radius: 20px;
            padding: 28px 32px;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
        }
        .masters-hero::before {
            content: "";
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, rgba(255,255,255,0) 70%);
        }
        .masters-hero::after {
            content: "";
            position: absolute;
            bottom: -70px; right: 150px;
            width: 170px; height: 170px;
            border-radius: 50%;
            background: rgba(255,255,255,.03);
        }
        .masters-hero-inner {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .masters-hero h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .masters-hero p {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 14px;
        }
        .hero-stats {
            display: flex;
            gap: 28px;
            align-items: center;
        }
        .hero-stat {
            text-align: right;
        }
        .hero-stat .num {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1;
        }
        .hero-stat .lbl {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 600;
            margin-top: 4px;
        }

        /* ===== Tab Navigation Pills ===== */
        .master-tabs-bar {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 8px;
            display: flex;
            gap: 6px;
            overflow-x: auto;
            box-shadow: var(--shadow-card);
        }
        .master-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-secondary);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
            font-family: inherit;
            text-decoration: none;
        }
        .master-tab-btn:hover {
            color: var(--text-primary);
            background: #F1F5F9;
        }
        .master-tab-btn.active {
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #FFFFFF !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
        }
        .tab-badge {
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            background: #F1F5F9;
            color: var(--text-secondary);
            transition: all .2s ease;
        }
        .master-tab-btn:hover .tab-badge {
            background: #E2E8F0;
            color: var(--text-primary);
        }
        .master-tab-btn.active .tab-badge {
            background: rgba(255, 255, 255, 0.22);
            color: #FFFFFF;
        }

        /* ===== Tab Panels ===== */
        .tab-panel {
            display: none;
        }
        .tab-panel.active {
            display: block;
            animation: panelFadeIn .22s ease;
        }

        /* ===== Section Layout Card ===== */
        .master-section-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .master-section-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            background: #FAFBFC;
        }
        .sec-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .sec-icon-box {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .sec-icon-box.services         { background: #EFF6FF; color: #2563EB; }
        .sec-icon-box.senders          { background: #ECFDF5; color: #059669; }
        .sec-icon-box.groups           { background: #EEF2FF; color: #4F46E5; }
        .sec-icon-box.prospect_sources { background: #FFFBEB; color: #D97706; }

        .sec-title-text h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
            color: var(--text-primary);
        }
        .sec-title-text p {
            margin: 3px 0 0;
            font-size: 13px;
            color: var(--text-secondary);
        }

        /* Add Item Inline Form */
        .inline-add-form {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 320px;
        }
        .inline-input-wrap {
            position: relative;
            flex: 1;
        }
        .inline-input-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }
        .inline-add-form input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: inherit;
            font-size: 13.5px;
            background: var(--surface);
            color: var(--text-primary);
            transition: all .15s ease;
        }
        .inline-add-form input:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        .btn-add-item {
            height: 42px;
            padding: 0 18px;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 13.5px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
            transition: all .18s ease;
            white-space: nowrap;
        }
        .btn-add-item:hover {
            background: linear-gradient(135deg, #1D4ED8, #1E40AF);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.3);
        }

        /* ===== Table Styling ===== */
        .master-table-wrap {
            overflow-x: auto;
        }
        .master-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .master-table thead th {
            text-align: left;
            padding: 13px 24px;
            background: #F8FAFC;
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .master-table tbody td {
            padding: 15px 24px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: var(--text-primary);
        }
        .master-table tbody tr {
            transition: background .15s ease;
        }
        .master-table tbody tr:hover {
            background: #F8FAFC;
        }
        .master-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .item-name-cell {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-primary);
        }

        /* Usage Badge */
        .usage-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
        }
        .usage-badge.in-use {
            background: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #BFDBFE;
        }
        .usage-badge.empty {
            background: #F1F5F9;
            color: #64748B;
            border: 1px solid #E2E8F0;
        }
        .usage-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Action Buttons */
        .actions-cell {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn-act {
            width: 34px; height: 34px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s ease;
            color: var(--text-secondary);
            font-family: inherit;
        }
        .btn-act:hover {
            background: var(--primary-50);
            color: var(--primary-600);
            border-color: var(--primary-200);
        }
        .btn-act.btn-delete:hover {
            background: #FEE2E2;
            color: #DC2626;
            border-color: #FECACA;
        }

        /* Grid View Mode */
        .all-grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .grid-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .grid-card-header {
            padding: 16px 20px;
            background: #FAFBFC;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .grid-card-body {
            padding: 16px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Modal Edit */
        .master-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .master-modal-overlay.open {
            display: flex;
            animation: modalFadeIn .2s ease;
        }
        .master-modal-content {
            background: var(--surface);
            border-radius: 18px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
            border: 1px solid var(--border);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            animation: modalSlideUp .25s ease;
        }
        .master-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FAFBFC;
        }
        .master-modal-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-close-btn {
            background: transparent;
            border: none;
            font-size: 24px;
            line-height: 1;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 8px;
            transition: all .15s ease;
        }
        .modal-close-btn:hover {
            background: #F1F5F9;
            color: var(--text-primary);
        }
        .master-modal-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .master-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #FAFBFC;
        }

        @keyframes panelFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(16px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ===== Responsive ===== */
        @media (max-width: 1024px) {
            .all-grid-layout {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .masters-hero {
                padding: 22px;
            }
            .masters-hero h1 {
                font-size: 22px;
            }
            .inline-add-form {
                width: 100%;
                min-width: 0;
            }
            .master-section-header {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <div class="masters-page">

        {{-- Hero Header --}}
        <div class="masters-hero">
            <div class="masters-hero-inner">
                <div>
                    <h1>Kelola Master Data</h1>
                    <p>Konfigurasi data referensi untuk Layanan/Jasa, Pengirim, Group, dan Sumber Prospek.</p>
                </div>
                <div class="hero-stats">
                    @php
                        $totalMasterItems = $services->count() + $senders->count() + $groups->count() + $prospect_sources->count();
                    @endphp
                    <div class="hero-stat">
                        <div class="num">{{ $totalMasterItems }}</div>
                        <div class="lbl">Total Referensi</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Navigation Pills --}}
        <div class="master-tabs-bar">
            <button type="button" class="master-tab-btn active" data-tab="services">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <span>Layanan / Jasa</span>
                <span class="tab-badge">{{ $services->count() }}</span>
            </button>
            <button type="button" class="master-tab-btn" data-tab="senders">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                <span>Pengirim Prospek</span>
                <span class="tab-badge">{{ $senders->count() }}</span>
            </button>
            <button type="button" class="master-tab-btn" data-tab="groups">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Group</span>
                <span class="tab-badge">{{ $groups->count() }}</span>
            </button>
            <button type="button" class="master-tab-btn" data-tab="prospect_sources">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                <span>Sumber Prospek</span>
                <span class="tab-badge">{{ $prospect_sources->count() }}</span>
            </button>
            <button type="button" class="master-tab-btn" data-tab="all_grid">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Tampilkan Semua (Grid)</span>
            </button>
        </div>

        {{-- Tab Panels --}}
        @foreach ($resources as $resKey => $resCfg)
            <div class="tab-panel {{ $loop->first ? 'active' : '' }}" id="panel_{{ $resKey }}">
                <div class="master-section-card">
                    <div class="master-section-header">
                        <div class="sec-title-wrap">
                            <div class="sec-icon-box {{ $resKey }}">
                                @if ($resKey === 'services')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                @elseif ($resKey === 'senders')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                @elseif ($resKey === 'groups')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                @endif
                            </div>
                            <div class="sec-title-text">
                                <h3>{{ $resCfg['label'] }}</h3>
                                <p>{{ $resCfg['desc'] }}</p>
                            </div>
                        </div>

                        {{-- Add Form --}}
                        <form method="POST" action="{{ route('admin.masters.store', $resKey) }}" class="inline-add-form">
                            @csrf
                            <div class="inline-input-wrap">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                <input type="text" name="name" placeholder="Tambah {{ strtolower($resCfg['label']) }}..." required>
                            </div>
                            <button type="submit" class="btn-add-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                <span>Tambah</span>
                            </button>
                        </form>
                    </div>

                    <div class="master-table-wrap">
                        <table class="master-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th>Nama Referensi</th>
                                    <th>Penggunaan pada Data Prospek</th>
                                    <th>Terakhir Diubah</th>
                                    <th style="text-align: right; width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (${$resKey} as $item)
                                    <tr>
                                        <td style="text-align: center; font-weight: 700; color: var(--text-secondary); font-size: 13px;">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <span class="item-name-cell">{{ $item->name }}</span>
                                        </td>
                                        <td>
                                            @if ($item->prospects_count > 0)
                                                <span class="usage-badge in-use">
                                                    <span class="usage-dot"></span>
                                                    <span>{{ $item->prospects_count }} Data Prospek</span>
                                                </span>
                                            @else
                                                <span class="usage-badge empty">
                                                    <span class="usage-dot"></span>
                                                    <span>Belum Digunakan</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td style="color: var(--text-muted); font-size: 13px;">
                                            {{ $item->updated_at ? $item->updated_at->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                                        </td>
                                        <td>
                                            <div class="actions-cell">
                                                <button type="button" class="btn-act btn-edit-master"
                                                    title="Edit Data"
                                                    data-resource="{{ $resKey }}"
                                                    data-resource-label="{{ $resCfg['label'] }}"
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->name }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                </button>

                                                <form method="POST" action="{{ route('admin.masters.destroy', [$resKey, $item->id]) }}" style="display:inline; margin:0;"
                                                    data-confirm="{{ $item->prospects_count > 0 ? 'Data ini digunakan oleh ' . $item->prospects_count . ' prospek. Yakin ingin menghapus?' : 'Hapus data ' . $item->name . '?' }}"
                                                    data-confirm-title="Hapus Master Data">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-act btn-delete" title="Hapus Data">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="5" style="text-align: center; padding: 44px 20px; color: var(--text-muted);">
                                            <div style="font-weight: 700; font-size: 14px; color: var(--text-primary); margin-bottom: 4px;">Belum ada data {{ strtolower($resCfg['label']) }}</div>
                                            <div style="font-size: 12.5px;">Gunakan formulir di atas untuk menambahkan data baru.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Panel All (Grid Mode) --}}
        <div class="tab-panel" id="panel_all_grid">
            <div class="all-grid-layout">
                @foreach ($resources as $resKey => $resCfg)
                    <div class="grid-card">
                        <div class="grid-card-header">
                            <div class="sec-title-wrap">
                                <div class="sec-icon-box {{ $resKey }}" style="width: 36px; height: 36px; border-radius: 10px;">
                                    @if ($resKey === 'services')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                    @elseif ($resKey === 'senders')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    @elseif ($resKey === 'groups')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    @else
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: var(--text-primary);">{{ $resCfg['label'] }}</h4>
                                </div>
                            </div>
                            <span class="tab-badge" style="font-size: 11.5px; padding: 3px 10px;">{{ ${$resKey}->count() }} item</span>
                        </div>

                        <div class="grid-card-body">
                            {{-- Add Form --}}
                            <form method="POST" action="{{ route('admin.masters.store', $resKey) }}" style="display: flex; gap: 8px;">
                                @csrf
                                <input type="text" name="name" placeholder="Tambah {{ strtolower($resCfg['label']) }}..." required
                                    style="flex: 1; padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; font-family: inherit;">
                                <button type="submit" class="btn-add-item" style="height: 38px; padding: 0 14px; font-size: 12.5px;">
                                    + Tambah
                                </button>
                            </form>

                            <div style="border: 1px solid var(--border); border-radius: 10px; overflow: hidden;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                    <tbody>
                                        @forelse (${$resKey} as $item)
                                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                                <td style="padding: 10px 14px; font-weight: 700; color: var(--text-primary);">
                                                    {{ $item->name }}
                                                </td>
                                                <td style="padding: 10px 14px; text-align: center; width: 110px;">
                                                    <span class="usage-badge {{ $item->prospects_count > 0 ? 'in-use' : 'empty' }}" style="font-size: 11px; padding: 2px 8px;">
                                                        {{ $item->prospects_count }} Prospek
                                                    </span>
                                                </td>
                                                <td style="padding: 10px 14px; text-align: right; width: 80px;">
                                                    <div style="display: inline-flex; gap: 4px;">
                                                        <button type="button" class="btn-act btn-edit-master" style="width: 28px; height: 28px;"
                                                            title="Edit Data"
                                                            data-resource="{{ $resKey }}"
                                                            data-resource-label="{{ $resCfg['label'] }}"
                                                            data-id="{{ $item->id }}"
                                                            data-name="{{ $item->name }}">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        </button>
                                                        <form method="POST" action="{{ route('admin.masters.destroy', [$resKey, $item->id]) }}" style="display:inline; margin:0;"
                                                            data-confirm="{{ $item->prospects_count > 0 ? 'Data ini digunakan oleh ' . $item->prospects_count . ' prospek. Yakin ingin menghapus?' : 'Hapus data ' . $item->name . '?' }}"
                                                            data-confirm-title="Hapus Master Data">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn-act btn-delete" style="width: 28px; height: 28px;" title="Hapus Data">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 18px; font-size: 12.5px;">
                                                    Belum ada data {{ strtolower($resCfg['label']) }}.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal Edit Master Data --}}
    <div class="master-modal-overlay" id="masterEditModal">
        <div class="master-modal-content">
            <div class="master-modal-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary-600);"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span id="modalMasterTitle">Edit Master Data</span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeMasterModal()">&times;</button>
            </div>
            <form method="POST" id="editMasterForm" action="">
                @csrf @method('PUT')
                <div class="master-modal-body">
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label for="modalItemName" style="font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .06em;">Nama Referensi</label>
                        <input type="text" id="modalItemName" name="name" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 13.5px; color: var(--text-primary);">
                    </div>
                </div>
                <div class="master-modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeMasterModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchMasterTab(tabKey) {
            document.querySelectorAll('.master-tab-btn').forEach(function (btn) {
                btn.classList.toggle('active', btn.getAttribute('data-tab') === tabKey);
            });
            document.querySelectorAll('.tab-panel').forEach(function (panel) {
                panel.classList.toggle('active', panel.id === 'panel_' + tabKey);
            });
            try {
                localStorage.setItem('active_master_tab', tabKey);
                window.location.hash = tabKey;
            } catch (e) {}
        }

        function openMasterEditModal(btn) {
            const modal = document.getElementById('masterEditModal');
            const form = document.getElementById('editMasterForm');
            const resource = btn.getAttribute('data-resource');
            const resourceLabel = btn.getAttribute('data-resource-label');
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');

            document.getElementById('modalMasterTitle').innerText = 'Edit ' + resourceLabel;
            document.getElementById('modalItemName').value = name;
            form.action = "{{ url('admin/masters') }}/" + resource + "/" + id;

            modal.classList.add('open');
            setTimeout(() => document.getElementById('modalItemName').focus(), 50);
        }

        function closeMasterModal() {
            const modal = document.getElementById('masterEditModal');
            modal.classList.remove('open');
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Tab clicks
            document.querySelectorAll('.master-tab-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    switchMasterTab(this.getAttribute('data-tab'));
                });
            });

            // Edit button clicks
            document.querySelectorAll('.btn-edit-master').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openMasterEditModal(this);
                });
            });

            // Close modal when clicking outside content
            const modal = document.getElementById('masterEditModal');
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeMasterModal();
                }
            });

            // ESC key to close modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMasterModal();
                }
            });

            // Restore active tab from hash or localStorage
            const hash = window.location.hash.replace('#', '');
            const saved = localStorage.getItem('active_master_tab');
            const targetTab = hash || saved || 'services';
            const targetBtn = document.querySelector('.master-tab-btn[data-tab="' + targetTab + '"]');
            if (targetBtn) {
                switchMasterTab(targetTab);
            }
        });
    </script>
@endsection
