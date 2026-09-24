@extends('layouts.app')

@section('title', 'Kelola Pengguna — Admin')

@section('content')
    <style>
        .users-page {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* ===== Hero Header ===== */
        .users-hero {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            border-radius: 20px;
            padding: 28px 32px;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
        }
        .users-hero::before {
            content: "";
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.18) 0%, rgba(255,255,255,0) 70%);
        }
        .users-hero::after {
            content: "";
            position: absolute;
            bottom: -80px; right: 140px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,.03);
        }
        .users-hero-inner {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .users-hero h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .users-hero p {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 14px;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 24px;
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
        .hero-btn {
            background: #FFFFFF;
            color: var(--primary-700);
            padding: 11px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 6px 16px rgba(0,0,0,.15);
            transition: all .2s ease;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .hero-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,.22);
            color: var(--primary-700);
            text-decoration: none;
        }

        /* ===== Quick Stats ===== */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }
        .qstat {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-card);
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .qstat:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .qstat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .qstat-icon.blue   { background: #EFF6FF; color: #2563EB; }
        .qstat-icon.green  { background: #ECFDF5; color: #059669; }
        .qstat-icon.indigo { background: #EEF2FF; color: #4F46E5; }
        .qstat-icon.amber  { background: #FFFBEB; color: #D97706; }
        .qstat-val {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1;
            color: var(--text-primary);
        }
        .qstat-lbl {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
            margin-top: 4px;
        }

        /* ===== Tambah User Card ===== */
        .create-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            transition: all .2s ease;
        }
        .create-card-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FAFBFC;
            gap: 12px;
            flex-wrap: wrap;
        }
        .create-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        .create-card-title .icon-wrap {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: var(--primary-50);
            color: var(--primary-600);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .role-notice-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: #FEF3C7;
            color: #92400E;
            border: 1px solid #FCD34D;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
        }
        .create-form {
            padding: 20px 22px;
            display: grid;
            grid-template-columns: repeat(4, 1fr) 1.2fr auto;
            gap: 14px;
            align-items: end;
        }
        .field-group {
            display: flex;
            flex-direction: column;
            margin: 0;
        }
        .field-group label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 6px;
        }
        .field-group input,
        .field-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            font-family: inherit;
            font-size: 13.5px;
            color: var(--text-primary);
            transition: all .15s ease;
        }
        .field-group input:focus,
        .field-group select:focus {
            outline: 0;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }
        .field-select-wrap {
            position: relative;
        }
        .field-select-wrap::after {
            content: "";
            position: absolute;
            right: 14px;
            top: 50%;
            width: 8px; height: 8px;
            border-right: 2px solid var(--text-muted);
            border-bottom: 2px solid var(--text-muted);
            transform: translateY(-70%) rotate(45deg);
            pointer-events: none;
        }
        .field-select-wrap select {
            padding-right: 36px;
            appearance: none;
            -webkit-appearance: none;
        }
        .btn-submit-create {
            height: 42px;
            padding: 0 20px;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 13.5px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22);
            transition: all .18s ease;
            white-space: nowrap;
        }
        .btn-submit-create:hover {
            background: linear-gradient(135deg, #1D4ED8, #1E40AF);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.32);
        }

        /* ===== Filter Card ===== */
        .filter-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .filter-form {
            display: grid;
            grid-template-columns: 1.6fr 1fr auto;
            gap: 14px;
            align-items: end;
            padding: 16px 22px;
        }
        .filter-search {
            position: relative;
        }
        .filter-search .input-wrap {
            position: relative;
        }
        .filter-search svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }
        .filter-search input {
            padding-left: 40px;
        }
        .filter-actions {
            display: flex;
            gap: 8px;
        }

        /* ===== Table Card ===== */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .table-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            gap: 12px;
            flex-wrap: wrap;
        }
        .table-title {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            color: var(--text-primary);
        }
        .table-subtitle {
            font-size: 12.5px;
            color: var(--text-secondary);
            margin: 3px 0 0;
        }
        .table-wrap {
            overflow-x: auto;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .users-table thead th {
            text-align: left;
            padding: 13px 20px;
            background: #F8FAFC;
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .users-table tbody td {
            padding: 15px 20px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: var(--text-primary);
        }
        .users-table tbody tr {
            transition: background .15s ease;
        }
        .users-table tbody tr:hover {
            background: #F8FAFC;
        }
        .users-table tbody tr:last-child td {
            border-bottom: 0;
        }

        /* User Profile Cell */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13.5px;
            color: #FFFFFF;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .user-avatar.super_admin       { background: linear-gradient(135deg, #6366F1, #4338CA); }
        .user-avatar.manager_marketing { background: linear-gradient(135deg, #3B82F6, #1D4ED8); }
        .user-avatar.marketing         { background: linear-gradient(135deg, #10B981, #047857); }
        .user-avatar.cs                { background: linear-gradient(135deg, #F59E0B, #D97706); }
        .user-avatar.default           { background: linear-gradient(135deg, #94A3B8, #64748B); }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .user-name {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-username {
            font-size: 12px;
            color: var(--text-muted);
            font-family: monospace;
        }
        .badge-self {
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            background: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
        }

        /* Role Badges */
        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .role-pill-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }
        .role-pill.super_admin       { background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; }
        .role-pill.manager_marketing { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
        .role-pill.marketing         { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }
        .role-pill.cs                { background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; }
        .role-pill.default           { background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; }

        .email-cell {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            font-size: 13px;
        }

        /* Action Buttons */
        .actions-cell {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }
        .action-btn {
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
            text-decoration: none;
            font-family: inherit;
        }
        .action-btn:hover {
            background: var(--primary-50);
            color: var(--primary-600);
            border-color: var(--primary-200);
            text-decoration: none;
        }
        .action-btn.btn-delete:hover {
            background: #FEE2E2;
            color: #DC2626;
            border-color: #FECACA;
        }
        .action-btn:disabled,
        .action-btn.disabled {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Empty State */
        .empty-row td {
            text-align: center;
            padding: 48px 24px !important;
            color: var(--text-muted);
        }
        .empty-icon {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: var(--primary-50);
            color: var(--primary-500);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        /* Pagination */
        .table-pagination {
            padding: 14px 22px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            background: #FAFBFC;
        }

        /* ===== Modal Edit User ===== */
        .user-modal-overlay {
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
        .user-modal-overlay.open {
            display: flex;
            animation: modalFadeIn .2s ease;
        }
        .user-modal-content {
            background: var(--surface);
            border-radius: 18px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
            border: 1px solid var(--border);
            width: 100%;
            max-width: 540px;
            overflow: hidden;
            animation: modalSlideUp .25s ease;
        }
        .user-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FAFBFC;
        }
        .user-modal-header h3 {
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
        .user-modal-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .user-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #FAFBFC;
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
        @media (max-width: 1100px) {
            .create-form {
                grid-template-columns: 1fr 1fr;
            }
            .create-form .btn-submit-create {
                grid-column: 1 / -1;
            }
        }
        @media (max-width: 900px) {
            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            .filter-form {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 640px) {
            .users-hero {
                padding: 22px;
            }
            .users-hero h1 {
                font-size: 22px;
            }
            .hero-actions {
                width: 100%;
                justify-content: space-between;
            }
            .create-form {
                grid-template-columns: 1fr;
                padding: 16px;
            }
            .quick-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="users-page">

        {{-- Hero Header --}}
        <div class="users-hero">
            <div class="users-hero-inner">
                <div>
                    <h1>Kelola Pengguna Sistem</h1>
                    <p>Manajemen akun tim Marketing, Customer Service, dan Manager Marketing.</p>
                </div>
                <div class="hero-actions">
                    <div class="hero-stat">
                        <div class="num">{{ $users->total() }}</div>
                        <div class="lbl">Total Pengguna</div>
                    </div>
                    <a href="#tambahUserSection" class="hero-btn" onclick="document.getElementById('nameInput').focus();">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah User
                    </a>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="quick-stats">
            <div class="qstat">
                <div class="qstat-icon blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $stats['total'] }}</div>
                    <div class="qstat-lbl">Total Pengguna</div>
                </div>
            </div>
            <div class="qstat">
                <div class="qstat-icon green">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m16 12-4-4-4 4"/><path d="M12 16V8"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $stats['marketing'] }}</div>
                    <div class="qstat-lbl">Marketing</div>
                </div>
            </div>
            <div class="qstat">
                <div class="qstat-icon indigo">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $stats['manager_marketing'] }}</div>
                    <div class="qstat-lbl">Manager Marketing</div>
                </div>
            </div>
            <div class="qstat">
                <div class="qstat-icon amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $stats['cs'] }}</div>
                    <div class="qstat-lbl">Customer Service</div>
                </div>
            </div>
        </div>

        {{-- Tambah Pengguna Baru Card --}}
        <div class="create-card" id="tambahUserSection">
            <div class="create-card-header">
                <h3 class="create-card-title">
                    <span class="icon-wrap">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    </span>
                    Tambah Pengguna Baru
                </h3>
                <span class="role-notice-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Role Super Admin tidak dapat ditambahkan
                </span>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="create-form">
                @csrf
                <div class="field-group">
                    <label for="nameInput">Nama Lengkap</label>
                    <input type="text" id="nameInput" name="name" placeholder="mis. Budi Santoso" value="{{ old('name') }}" required>
                </div>
                <div class="field-group">
                    <label for="usernameInput">Username</label>
                    <input type="text" id="usernameInput" name="username" placeholder="mis. budi_mkt" value="{{ old('username') }}" required>
                </div>
                <div class="field-group">
                    <label for="emailInput">Alamat Email</label>
                    <input type="email" id="emailInput" name="email" placeholder="budi@hivefive.id" value="{{ old('email') }}" required>
                </div>
                <div class="field-group">
                    <label for="passwordInput">Password</label>
                    <input type="password" id="passwordInput" name="password" placeholder="Min. 6 karakter" required minlength="6">
                </div>
                <div class="field-group">
                    <label for="roleInput">Peran / Role</label>
                    <div class="field-select-wrap">
                        <select id="roleInput" name="role_id" required>
                            <option value="">Pilih Role...</option>
                            @foreach ($creatableRoles as $r)
                                <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-submit-create">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Simpan User
                </button>
            </form>
        </div>

        {{-- Filter & Search Card --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form">
                <div class="field-group filter-search">
                    <label>Pencarian</label>
                    <div class="input-wrap">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, username, atau email...">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                </div>
                <div class="field-group">
                    <label>Filter Role</label>
                    <div class="field-select-wrap">
                        <select name="role_id">
                            <option value="">Semua Role</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary" style="height:42px; border-radius:10px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Terapkan
                    </button>
                    @if (request('q') || request('role_id'))
                        <a href="{{ route('admin.users.index') }}" class="btn-reset" style="height:42px; border-radius:10px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="table-card">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Daftar Pengguna</h3>
                    <p class="table-subtitle">Menampilkan {{ $users->count() }} dari total {{ $users->total() }} pengguna</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Pengguna</th>
                            <th>Email</th>
                            <th>Role / Peran</th>
                            <th>Terdaftar</th>
                            <th style="text-align: right; width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                            @php
                                $roleSlug = $u->role?->slug ?? 'default';
                                $initials = strtoupper(substr($u->name, 0, 2));
                                $isSelf = $u->id === auth()->id();
                                $isSuperAdmin = $roleSlug === 'super_admin';
                            @endphp
                            <tr>
                                <td style="text-align: center; font-weight: 700; color: var(--text-secondary); font-size: 13px;">
                                    {{ ($users->firstItem() ?? 1) + $loop->index }}
                                </td>
                                <td>
                                    <div class="user-profile">
                                        <div class="user-avatar {{ $roleSlug }}">{{ $initials }}</div>
                                        <div class="user-info">
                                            <div class="user-name">
                                                <span>{{ $u->name }}</span>
                                                @if ($isSelf)
                                                    <span class="badge-self">Anda</span>
                                                @endif
                                            </div>
                                            <div class="user-username">&#64;{{ $u->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="email-cell">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted);"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        <span>{{ $u->email }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-pill {{ $roleSlug }}">
                                        <span class="role-pill-dot"></span>
                                        <span>{{ $u->role?->name ?? 'Belum ada role' }}</span>
                                    </span>
                                </td>
                                <td style="color: var(--text-secondary); font-size: 13px;">
                                    <div>{{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '-' }}</div>
                                    <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">{{ $u->created_at ? $u->created_at->format('H:i') : '' }} WIB</div>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        {{-- Edit Button --}}
                                        <button type="button" class="action-btn btn-edit-user"
                                            title="Edit Pengguna"
                                            data-id="{{ $u->id }}"
                                            data-name="{{ $u->name }}"
                                            data-username="{{ $u->username }}"
                                            data-email="{{ $u->email }}"
                                            data-role-id="{{ $u->role_id }}"
                                            data-role-slug="{{ $roleSlug }}"
                                            data-role-name="{{ $u->role?->name ?? 'Super Admin' }}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>

                                        {{-- Delete Button --}}
                                        @if (!$isSelf && !$isSuperAdmin)
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" style="display:inline; margin:0;"
                                                data-confirm="Hapus pengguna {{ $u->name }}? Data akun ini tidak dapat dikembalikan."
                                                data-confirm-title="Hapus Pengguna">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="action-btn btn-delete" title="Hapus Pengguna">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="action-btn disabled" title="{{ $isSelf ? 'Tidak dapat hapus akun sendiri' : 'Role Super Admin dilindungi' }}" disabled>
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="6">
                                    <div class="empty-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </div>
                                    <div style="font-weight:700; color:var(--text-primary); margin-bottom:4px;">Tidak ada pengguna ditemukan</div>
                                    <div style="font-size:12.5px;">Coba ubah kata kunci pencarian atau filter role.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-pagination">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Edit Pengguna --}}
    <div class="user-modal-overlay" id="userEditModal">
        <div class="user-modal-content">
            <div class="user-modal-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary-600);"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Data Pengguna
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" id="editUserForm" action="">
                @csrf @method('PUT')
                <div class="user-modal-body">
                    <div class="field-group">
                        <label for="modalEditName">Nama Lengkap</label>
                        <input type="text" id="modalEditName" name="name" required>
                    </div>
                    <div class="field-group">
                        <label for="modalEditUsername">Username</label>
                        <input type="text" id="modalEditUsername" name="username" required>
                    </div>
                    <div class="field-group">
                        <label for="modalEditEmail">Alamat Email</label>
                        <input type="email" id="modalEditEmail" name="email" required>
                    </div>
                    <div class="field-group">
                        <label for="modalEditPassword">Password Baru (Opsional)</label>
                        <input type="password" id="modalEditPassword" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
                    </div>

                    {{-- Dynamic Role Section: Locked for Super Admin, Selectable for others --}}
                    <div class="field-group" id="editRoleGroup">
                        <label for="modalEditRole">Peran / Role</label>
                        <input type="hidden" id="modalLockedRoleId" name="role_id" value="" disabled>
                        <div id="roleSelectWrap" class="field-select-wrap">
                            <select id="modalEditRole" name="role_id" required>
                                @foreach ($creatableRoles as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="roleLockedNotice" style="display:none; padding:10px 14px; background:#EEF2FF; border:1px solid #C7D2FE; border-radius:10px; color:#4338CA; font-weight:700; font-size:13px; align-items:center; gap:8px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span>Super Admin (Role sistem utama terkunci &amp; tidak dapat diubah)</span>
                        </div>
                    </div>
                </div>
                <div class="user-modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(btn) {
            const modal = document.getElementById('userEditModal');
            const form = document.getElementById('editUserForm');
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const username = btn.getAttribute('data-username');
            const email = btn.getAttribute('data-email');
            const roleId = btn.getAttribute('data-role-id');
            const roleSlug = btn.getAttribute('data-role-slug');

            form.action = "{{ url('admin/users') }}/" + id;
            document.getElementById('modalEditName').value = name;
            document.getElementById('modalEditUsername').value = username;
            document.getElementById('modalEditEmail').value = email;
            document.getElementById('modalEditPassword').value = '';

            const roleSelectWrap = document.getElementById('roleSelectWrap');
            const roleSelect = document.getElementById('modalEditRole');
            const roleLockedNotice = document.getElementById('roleLockedNotice');
            const lockedRoleIdInput = document.getElementById('modalLockedRoleId');

            if (roleSlug === 'super_admin') {
                roleSelectWrap.style.display = 'none';
                roleSelect.disabled = true;
                roleLockedNotice.style.display = 'flex';
                lockedRoleIdInput.value = roleId;
                lockedRoleIdInput.disabled = false;
            } else {
                roleSelectWrap.style.display = 'block';
                roleSelect.disabled = false;
                roleLockedNotice.style.display = 'none';
                roleSelect.value = roleId;
                lockedRoleIdInput.disabled = true;
            }

            modal.classList.add('open');
        }

        function closeEditModal() {
            const modal = document.getElementById('userEditModal');
            modal.classList.remove('open');
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-edit-user').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openEditModal(this);
                });
            });

            // Close modal when clicking outside content
            const modal = document.getElementById('userEditModal');
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeEditModal();
                }
            });

            // ESC key to close modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeEditModal();
                }
            });
        });
    </script>
@endsection
