@extends('layouts.app')

@section('title', ($role === 'cs' ? 'Edit Data Prospek — ' : ($role === 'marketing' ? 'Update Prospek — ' : 'Edit Prospek — ')) . config('app.name'))

@php use Carbon\Carbon; @endphp

@push('styles')
<style>
    .pc-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
        gap: 22px;
        align-items: start;
    }
    @media (max-width: 1023px) { .pc-wrap { grid-template-columns: 1fr; } }

    .pc-form {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow-card);
    }

    .pc-section {
        padding: 22px 22px 18px;
        border-bottom: 1px solid var(--border);
    }
    .pc-section:last-of-type { border-bottom: 0; }
    .pc-section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .pc-section-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-50);
        color: var(--primary-600);
        flex-shrink: 0;
    }
    .pc-section-icon.amber { background: #FEF3C7; color: #B45309; }
    .pc-section-icon.green { background: #ECFDF5; color: #047857; }
    .pc-section-icon.blue  { background: var(--primary-50); color: var(--primary-600); }
    .pc-section-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.01em;
    }
    .pc-section-sub {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0;
    }

    .pc-num {
        width: 22px; height: 22px;
        border-radius: 6px;
        background: var(--primary-50);
        color: var(--primary-600);
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pc-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    @media (max-width: 640px) { .pc-grid-2 { grid-template-columns: 1fr; } }

    .pc-field { margin-bottom: 0; }
    .pc-field label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .pc-field label .req { color: #DC2626; font-size: 11px; }
    .pc-field .hint {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 5px;
    }
    .pc-field .pc-error {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #B91C1C;
        margin-top: 6px;
        font-weight: 500;
    }
    .pc-field .pc-error::before {
        content: "!";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #FEE2E2;
        color: #B91C1C;
        font-size: 10px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .pc-input, .pc-select {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-family: inherit;
        font-size: 14px;
        color: var(--text-primary);
        background: var(--surface);
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .pc-input:hover, .pc-select:hover { border-color: #CBD5E1; }
    .pc-input:focus, .pc-select:focus {
        outline: 0;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        background: #fff;
    }
    .pc-input.is-invalid, .pc-select.is-invalid {
        border-color: #FCA5A5;
        background: #FEF2F2;
    }
    .pc-input.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .12);
        border-color: #DC2626;
    }
    .pc-select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%2364748B' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    /* flatpickr */
    .dt-wrap { position: relative; }
    .dt-wrap .pc-input {
        padding-right: 42px;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        cursor: pointer;
    }
    .dt-wrap.time .pc-input {
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cpolyline points='12 6 12 12 16 14'/%3E%3C/svg%3E");
    }
    .flatpickr-calendar {
        border-radius: 16px !important;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .14) !important;
        border: 1px solid var(--border) !important;
        font-family: inherit !important;
        padding: 14px !important;
        width: 340px !important;
        max-width: 340px !important;
    }
    .flatpickr-calendar .flatpickr-innerContainer,
    .flatpickr-calendar .flatpickr-days,
    .flatpickr-calendar .dayContainer {
        width: 100% !important; min-width: 100% !important; max-width: 100% !important;
    }
    .flatpickr-calendar .flatpickr-day {
        height: 40px !important;
        line-height: 40px !important;
        max-width: 40px !important;
        border-radius: 10px !important;
        font-weight: 600;
        font-size: 13.5px;
    }
    .flatpickr-calendar .flatpickr-day.selected {
        background: var(--primary-500) !important;
        border-color: var(--primary-500) !important;
    }

    .pc-submit {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 18px 22px;
        background: linear-gradient(to top, #F8FAFC, #FFFFFF);
        border-top: 1px solid var(--border);
        border-radius: 0 0 16px 16px;
    }
    .pc-submit-meta { font-size: 12px; color: var(--text-muted); }
    .pc-submit-actions { display: flex; gap: 10px; }

    /* Sidebar */
    .pc-side { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 84px; }
    .pc-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow-card);
        padding: 18px;
    }
    .pc-card h3 {
        font-size: 13px; font-weight: 700; color: var(--text-primary);
        margin: 0 0 14px; display: flex; align-items: center; gap: 8px;
    }
    .pc-card h3 .dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--primary-500);
    }
    .pc-summary-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 9px 0; font-size: 12.5px; border-bottom: 1px dashed var(--border); gap: 10px;
    }
    .pc-summary-row:last-child { border-bottom: 0; }
    .pc-summary-row .k { color: var(--text-secondary); font-weight: 500; }
    .pc-summary-row .v { color: var(--text-primary); font-weight: 600; text-align: right; }
    .pc-summary-row .v.badge { font-weight: 700; }

    .pc-tips { list-style: none; padding: 0; margin: 0; }
    .pc-tips li {
        display: flex;
        gap: 10px;
        padding: 8px 0;
        font-size: 12.5px;
        color: var(--text-secondary);
        line-height: 1.5;
    }
    .pc-tips li svg { flex-shrink: 0; margin-top: 1px; color: var(--primary-500); }

    .pc-error {
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; color: #B91C1C; margin-top: 6px; font-weight: 500;
    }
    .pc-error::before {
        content: "!"; display: inline-flex; align-items: center; justify-content: center;
        width: 16px; height: 16px; border-radius: 50%; background: #FEE2E2;
        color: #B91C1C; font-size: 10px; font-weight: 800; flex-shrink: 0;
    }

    [x-cloak] { display: none !important; }

    /* Status Selector Cards */
    .status-selector-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    @media (max-width: 720px) {
        .status-selector-grid { grid-template-columns: 1fr; }
    }

    .status-card-opt {
        position: relative;
        cursor: pointer;
        display: block;
        margin: 0;
    }
    .status-card-opt input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .status-card-inner {
        padding: 16px 14px;
        border: 2px solid var(--border);
        border-radius: 12px;
        background: var(--surface);
        transition: border-color .18s ease, background .18s ease, box-shadow .18s ease, transform .18s ease;
        display: flex;
        flex-direction: column;
        gap: 10px;
        height: 100%;
        box-sizing: border-box;
    }
    .status-card-opt:hover .status-card-inner {
        border-color: #CBD5E1;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    }
    .status-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .status-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform .18s ease;
    }
    .status-check-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border);
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: transparent;
        transition: all .18s ease;
    }
    .status-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.01em;
        margin-bottom: 2px;
    }
    .status-card-desc {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    /* Open */
    .status-card-opt.status-open .status-card-icon {
        background: #EFF6FF;
        color: #2563EB;
    }
    .status-card-opt.status-open.is-selected .status-card-inner,
    .status-card-opt.status-open input:checked ~ .status-card-inner {
        border-color: #2563EB;
        background: #F8FAFC;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .status-card-opt.status-open.is-selected .status-check-circle,
    .status-card-opt.status-open input:checked ~ .status-card-inner .status-check-circle {
        border-color: #2563EB;
        background: #2563EB;
        color: #fff;
    }

    /* Closing */
    .status-card-opt.status-closing .status-card-icon {
        background: #ECFDF5;
        color: #059669;
    }
    .status-card-opt.status-closing.is-selected .status-card-inner,
    .status-card-opt.status-closing input:checked ~ .status-card-inner {
        border-color: #10B981;
        background: #F0FDF4;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
    }
    .status-card-opt.status-closing.is-selected .status-check-circle,
    .status-card-opt.status-closing input:checked ~ .status-card-inner .status-check-circle {
        border-color: #10B981;
        background: #10B981;
        color: #fff;
    }

    /* Cancel */
    .status-card-opt.status-cancel .status-card-icon {
        background: #FEF2F2;
        color: #DC2626;
    }
    .status-card-opt.status-cancel.is-selected .status-card-inner,
    .status-card-opt.status-cancel input:checked ~ .status-card-inner {
        border-color: #EF4444;
        background: #FEF2F2;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }
    .status-card-opt.status-cancel.is-selected .status-check-circle,
    .status-card-opt.status-cancel input:checked ~ .status-card-inner .status-check-circle {
        border-color: #EF4444;
        background: #EF4444;
        color: #fff;
    }

    /* Closing Detail Box */
    .closing-detail-box {
        background: #F0FDF4;
        border-left: 4px solid #10B981;
    }
</style>
@endpush

@section('content')
    @php
        $isCs = ($role === 'cs');
        $isMarketing = ($role === 'marketing');
        $isManagerOrAdmin = in_array($role, ['manager_marketing', 'super_admin'], true);

        $slug = $prospect->status?->slug ?? '';
        $badgeClass = in_array($slug, ['open','closing','cancel']) ? 'badge-'.$slug : 'badge-default';
        $created = $prospect->created_at;
        $updated = $prospect->updated_at;
        $closingStatusId = $statuses->firstWhere('slug', 'closing')?->id ?? 2;
        $cancelStatusId = $statuses->firstWhere('slug', 'cancel')?->id ?? 3;

        $secNum = 1;
    @endphp

    <div class="page-header">
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                <a href="{{ route('prospects.index') }}" style="font-size:12px; color:var(--text-secondary);">Prospek</a>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted)"><polyline points="9 18 15 12 9 6"/></svg>
                <span style="font-size:12px; color:var(--text-primary); font-weight:600;">{{ $isCs ? 'Edit Data Prospek' : ($isMarketing ? 'Update Status' : 'Edit Prospek') }}</span>
            </div>
            <h1>{{ $isCs ? 'Edit Data Prospek' : ($isMarketing ? 'Update Prospek' : 'Edit Prospek') }}</h1>
            <p>
                @if ($isCs)
                    Edit informasi kontak, layanan, waktu masuk, dan assignment prospek <strong>{{ $prospect->client_phone }}</strong>.
                @elseif ($isMarketing)
                    Perbarui status, detail closing, dan catatan prospek <strong>{{ $prospect->client_phone }}</strong>.
                @else
                    Edit data kontak, status, closing, dan assignment prospek <strong>{{ $prospect->client_phone }}</strong>.
                @endif
            </p>
        </div>
        <a href="{{ route('prospects.index') }}" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- FORM UTAMA: CS Edit Data / Marketing Update  --}}
    {{-- ============================================ --}}
    <form method="POST" action="{{ route('prospects.update', $prospect) }}" class="pc-wrap" novalidate id="prospectEditForm"
          x-data="{
              statusId: '{{ old('status_id', $prospect->status_id) }}',
              isClosing() {
                  return String(this.statusId) === '{{ $closingStatusId }}';
              },
              isCancel() {
                  return String(this.statusId) === '{{ $cancelStatusId }}';
              },
              setNow() {
                  const now = new Date();
                  const pad = (n) => String(n).padStart(2, '0');
                  const str = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}`;
                  const input = document.querySelector('input[name=closed_at]');
                  if (input && input._flatpickr) {
                      input._flatpickr.setDate(str, true);
                  } else if (input) {
                      input.value = str;
                  }
              }
          }"
          @submit="handleProspectSubmit($event)">
        @csrf @method('PUT')

        <div class="pc-form">
            {{-- ======================================================== --}}
            {{-- DATA PROSPEK BARU (CS & MANAGER/ADMIN DAPAT EDIT DATA INI) --}}
            {{-- ======================================================== --}}
            @if (!$isMarketing)
                {{-- Section: Informasi Klien --}}
                <div class="pc-section">
                    <div class="pc-section-head">
                        <span class="pc-num">{{ $secNum++ }}</span>
                        <div>
                            <p class="pc-section-title">Informasi Klien</p>
                            <p class="pc-section-sub">Data kontak utama klien yang akan dihubungi.</p>
                        </div>
                    </div>

                    <div class="pc-grid-2">
                        <div class="pc-field">
                            <label>Nomor Telepon / WhatsApp <span class="req" style="color:#DC2626;">*</span></label>
                            <input type="text" name="client_phone" id="inputClientPhone" class="pc-input @if($errors->has('client_phone')) is-invalid @endif" value="{{ old('client_phone', $prospect->client_phone) }}" required placeholder="08xxx atau 62xxx" maxlength="32" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-phone-input>
                            <div class="hint">Hanya angka (0-9), contoh: 081234567890</div>

                            <div id="phone-duplicate-alert" style="display:none; margin-top:8px; padding:10px 12px; background:#FEF2F2; border-left:4px solid #EF4444; border-radius:8px;">
                                <div style="display:flex; align-items:flex-start; gap:8px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <span id="phone-duplicate-msg" style="font-size:12px; color:#991B1B; font-weight:600; line-height:1.4;"></span>
                                </div>
                            </div>

                            @error('client_phone')
                                <div class="pc-error" style="margin-top:8px; padding:10px 12px; background:#FEF2F2; border-left:4px solid #EF4444; border-radius:8px; display:flex; align-items:flex-start; gap:8px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <span style="font-size:12px; color:#991B1B; font-weight:600; line-height:1.4;">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <div class="pc-field">
                            <label>Layanan / Jasa <span class="req" style="color:#DC2626;">*</span></label>
                            <select name="service_id" class="pc-select" required>
                                <option value="">— Pilih Layanan —</option>
                                @foreach ($services as $s)
                                    <option value="{{ $s->id }}" {{ old('service_id', $prospect->service_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <div class="hint">Pilih jenis layanan yang diminati klien.</div>
                            @error('service_id')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Waktu Masuk --}}
                <div class="pc-section">
                    <div class="pc-section-head">
                        <span class="pc-num">{{ $secNum++ }}</span>
                        <div>
                            <p class="pc-section-title">Waktu Masuk Prospek</p>
                            <p class="pc-section-sub">Kapan prospek ini diterima / masuk ke sistem.</p>
                        </div>
                    </div>

                    <div class="pc-grid-2">
                        <div class="pc-field">
                            <label>Tanggal <span class="req" style="color:#DC2626;">*</span></label>
                            <div class="dt-wrap">
                                <input type="text" name="entry_date" class="pc-input fp-date" value="{{ old('entry_date', $prospect->entry_date ? $prospect->entry_date->format('Y-m-d') : date('Y-m-d')) }}" required placeholder="Pilih tanggal" autocomplete="off">
                            </div>
                            @error('entry_date')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="pc-field">
                            <label>Jam <span class="req" style="color:#DC2626;">*</span></label>
                            <div class="dt-wrap time">
                                <input type="text" name="entry_time" class="pc-input fp-time" value="{{ old('entry_time', $prospect->entry_time ? substr($prospect->entry_time, 0, 5) : date('H:i')) }}" required placeholder="Pilih jam" autocomplete="off">
                            </div>
                            @error('entry_time')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Assignment & Sumber --}}
                <div class="pc-section">
                    <div class="pc-section-head">
                        <span class="pc-num">{{ $secNum++ }}</span>
                        <div>
                            <p class="pc-section-title">Assignment & Sumber</p>
                            <p class="pc-section-sub">Asal prospek dan siapa yang bertanggung jawab menindaklanjuti.</p>
                        </div>
                    </div>

                    <div class="pc-grid-2">
                        <div class="pc-field">
                            <label>Pengirim Prospek <span class="req" style="color:#DC2626;">*</span></label>
                            <select name="sender_id" class="pc-select" required>
                                <option value="">— Pilih Pengirim —</option>
                                @foreach ($senders as $s)
                                    <option value="{{ $s->id }}" {{ old('sender_id', $prospect->sender_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('sender_id')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="pc-field">
                            <label>Group <span class="req" style="color:#DC2626;">*</span></label>
                            <select name="group_id" class="pc-select" required>
                                <option value="">— Pilih Group —</option>
                                @foreach ($groups as $g)
                                    <option value="{{ $g->id }}" {{ old('group_id', $prospect->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="pc-field">
                            <label>Sumber Prospek <span style="font-size: 11px; font-weight: normal; color: #64748B;">(Opsional)</span></label>
                            <select name="source_id" class="pc-select">
                                <option value="">-</option>
                                @foreach ($sources as $s)
                                    @if ($s->name !== '-')
                                        <option value="{{ $s->id }}" {{ old('source_id', $prospect->source_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('source_id')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="pc-field">
                            <label>Marketing Penanggung Jawab <span class="req" style="color:#DC2626;">*</span></label>
                            <select name="marketing_user_id" class="pc-select" required>
                                <option value="">— Pilih Marketing —</option>
                                @foreach ($marketings as $m)
                                    <option value="{{ $m->id }}" {{ old('marketing_user_id', $prospect->marketing_user_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                            @error('marketing_user_id')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- STATUS & CLOSING (HANYA MARKETING & MANAGER/ADMIN)       --}}
            {{-- CS TIDAK MELIHAT FORM STATUS/CLOSING/CANCEL              --}}
            {{-- ======================================================== --}}
            @if (!$isCs)
                {{-- Section: Status Prospek (Interactive Card Selector) --}}
                <div class="pc-section">
                    <div class="pc-section-head">
                        <span class="pc-num">{{ $secNum++ }}</span>
                        <div>
                            <p class="pc-section-title">Status Prospek <span class="req" style="color:#DC2626;">*</span></p>
                            <p class="pc-section-sub">Pilih perkembangan status prospek saat ini.</p>
                        </div>
                    </div>

                    <div class="status-selector-grid">
                        @foreach ($statuses as $s)
                            @php
                                $isCur = old('status_id', $prospect->status_id) == $s->id;
                                $sSlug = $s->slug;
                                $themeClass = match($sSlug) {
                                    'open' => 'status-open',
                                    'closing' => 'status-closing',
                                    'cancel' => 'status-cancel',
                                    default => 'status-default'
                                };
                                $desc = match($sSlug) {
                                    'open' => 'Prospek aktif sedang difollow up',
                                    'closing' => 'Deal transaksi berhasil disepakati',
                                    'cancel' => 'Batal atau tidak berminat',
                                    default => 'Status prospek'
                                };
                            @endphp
                            <label class="status-card-opt {{ $themeClass }}" :class="{ 'is-selected': String(statusId) === '{{ $s->id }}' }">
                                <input type="radio" name="status_id" value="{{ $s->id }}"
                                       x-model="statusId"
                                       {{ $isCur ? 'checked' : '' }} required>
                                <div class="status-card-inner">
                                    <div class="status-card-head">
                                        <span class="status-card-icon">
                                            @if ($sSlug === 'open')
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            @elseif ($sSlug === 'closing')
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            @else
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                            @endif
                                        </span>
                                        <span class="status-check-circle" aria-hidden="true">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </span>
                                    </div>
                                    <div class="status-card-body">
                                        <div class="status-card-title">{{ $s->name }}</div>
                                        <div class="status-card-desc">{{ $desc }}</div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('status_id')
                        <div class="pc-error" style="margin-top:10px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Section: Detail Closing (Deal) — Hanya jika status = Closing --}}
                <div class="pc-section closing-detail-box" x-show="isClosing()" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="{{ old('status_id', $prospect->status_id) == $closingStatusId ? '' : 'display:none;' }}">
                    <div class="pc-section-head">
                        <span class="pc-num" style="background:#ECFDF5; color:#047857;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div>
                            <p class="pc-section-title">Detail Transaksi Closing</p>
                            <p class="pc-section-sub">Lengkapi waktu closing dan nilai transaksi yang disepakati.</p>
                        </div>
                    </div>

                    <div class="pc-grid-2">
                        <div class="pc-field">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                                <label style="margin:0;">Tanggal & Jam Closing <span class="req" style="color:#DC2626;">*</span></label>
                                <button type="button" @click="setNow()" style="background:none; border:none; color:var(--primary-600); font-size:11.5px; font-weight:600; cursor:pointer; padding:0; display:inline-flex; align-items:center; gap:4px;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Set Sekarang
                                </button>
                            </div>
                            <div class="dt-wrap">
                                <input type="text" name="closed_at" class="pc-input fp-datetime" value="{{ old('closed_at', $prospect->closed_at?->format('Y-m-d H:i')) }}" placeholder="Pilih tanggal & jam..." autocomplete="off">
                            </div>
                            <div class="hint">Kosongkan untuk otomatis mengisi waktu saat ini.</div>
                            @error('closed_at')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pc-field">
                            <label>Nominal Closing (Rp)</label>
                            <div style="position:relative;">
                                <input type="text"
                                       inputmode="numeric"
                                       name="nominal_closing"
                                       id="inputNominalClosing"
                                       class="pc-input @error('nominal_closing') is-invalid @enderror"
                                       value="{{ old('nominal_closing') !== null ? (is_numeric(str_replace('.', '', (string) old('nominal_closing'))) ? number_format((float) str_replace('.', '', (string) old('nominal_closing')), 0, ',', '.') : old('nominal_closing')) : ($prospect->nominal_closing ? number_format($prospect->nominal_closing, 0, ',', '.') : '') }}"
                                       placeholder="Contoh: 5.000.000"
                                       style="padding-left:44px;"
                                       oninput="formatNominalRupiah(this)">
                                <span style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-secondary); font-weight:700; font-size:13px; pointer-events:none;">Rp</span>
                            </div>
                            <div class="hint">Nilai transaksi dalam Rupiah (otomatis format titik ribuan).</div>
                            @error('nominal_closing')
                                <div class="pc-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- Section: Keterangan Prospek (Khusus Marketing & Manager, disembunyikan untuk CS) --}}
            @if (!$isCs)
                <div class="pc-section" x-show="!isCancel()" x-cloak>
                    <div class="pc-section-head">
                        <span class="pc-num">{{ $secNum++ }}</span>
                        <div>
                            <p class="pc-section-title">Keterangan Prospek</p>
                            <p class="pc-section-sub">Informasi keterangan seputar prospek ini.</p>
                        </div>
                    </div>

                    <div class="pc-field">
                        <label>Keterangan</label>
                        <textarea name="note" class="pc-input @error('note') is-invalid @enderror" rows="3" placeholder="Tulis keterangan prospek, kebutuhan klien, atau alasan status (opsional)...">{{ old('note', $prospect->note) }}</textarea>
                        <div class="hint">Maksimal 2.000 karakter. Kosongkan jika tidak ada.</div>
                        @error('note')
                            <div class="pc-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endif

            {{-- Alert Cancel: data akan otomatis dihapus jika disimpan (Khusus Marketing & Manager) --}}
            @if (!$isCs)
                <div class="pc-section" x-show="isCancel()" x-cloak style="background:#FEF2F2; border-left:4px solid #EF4444;">
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="width:36px; height:36px; border-radius:10px; background:#FEE2E2; color:#DC2626; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </div>
                        <div>
                            <h4 style="margin:0 0 4px; font-size:14px; font-weight:700; color:#991B1B;">Perhatian: Prospek Akan Dihapus</h4>
                            <p style="margin:0; font-size:12.5px; color:#B91C1C; line-height:1.5;">
                                Jika status <strong>Cancel</strong> disimpan, seluruh data prospek ini akan otomatis dihapus secara permanen dari sistem.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Submit Bar --}}
            <div class="pc-submit">
                @if ($isCs)
                    <span class="pc-submit-meta">Field bertanda <span style="color:#DC2626;">*</span> wajib diisi. Perubahan langsung tersimpan ke sistem.</span>
                    <div class="pc-submit-actions">
                        <a href="{{ route('prospects.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" id="btnSubmitProspect" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                @else
                    <span class="pc-submit-meta" x-show="!isCancel()">Form status prospek — perubahan status tercatat di riwayat.</span>
                    <span class="pc-submit-meta" x-show="isCancel()" x-cloak style="color:#DC2626; font-weight:700;">Data prospek akan otomatis dihapus saat disimpan.</span>
                    <div class="pc-submit-actions">
                        <a href="{{ route('prospects.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn" :class="isCancel() ? 'btn-danger' : 'btn-primary'" id="btnSubmitProspect">
                            <span x-show="!isCancel()" style="display:inline-flex; align-items:center; gap:6px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ $isManagerOrAdmin ? 'Simpan Perubahan' : 'Simpan Update' }}
                            </span>
                            <span x-show="isCancel()" x-cloak style="display:inline-flex; align-items:center; gap:6px; color:#B91C1C; font-weight:700;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Cancel & Hapus Prospek
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="pc-side">
            <div class="pc-card">
                <h3><span class="dot"></span> Info Prospek</h3>
                <div class="pc-summary-row">
                    <span class="k">Status saat ini</span>
                    <span class="v"><span class="badge {{ $badgeClass }}">{{ $prospect->status?->name ?? '—' }}</span></span>
                </div>
                <div class="pc-summary-row">
                    <span class="k">Client</span>
                    <span class="v">{{ $prospect->client_phone }}</span>
                </div>
                <div class="pc-summary-row">
                    <span class="k">Layanan</span>
                    <span class="v">{{ $prospect->service?->name ?? '—' }}</span>
                </div>
                <div class="pc-summary-row">
                    <span class="k">Marketing</span>
                    <span class="v">{{ $prospect->marketing?->name ?? '—' }}</span>
                </div>
                <div class="pc-summary-row">
                    <span class="k">Group / Sumber</span>
                    <span class="v">{{ $prospect->group?->name ?? '—' }} / {{ $prospect->source?->name ?? '—' }}</span>
                </div>
                @if (!$isCs)
                    <div class="pc-summary-row">
                        <span class="k">Nominal saat ini</span>
                        <span class="v">{{ $prospect->nominal_closing ? 'Rp '.number_format($prospect->nominal_closing, 0, ',', '.') : '—' }}</span>
                    </div>
                    <div class="pc-summary-row">
                        <span class="k">Closing saat ini</span>
                        <span class="v">{{ $prospect->closed_at?->format('d M Y, H:i') ?? '—' }}</span>
                    </div>
                @endif
                <div class="pc-summary-row">
                    <span class="k">Dibuat</span>
                    <span class="v">{{ $created ? $created->format('d M Y, H:i') : '—' }}</span>
                </div>
                <div class="pc-summary-row">
                    <span class="k">Update terakhir</span>
                    <span class="v">{{ $updated ? $updated->diffForHumans() : '—' }}</span>
                </div>
            </div>

            <div class="pc-card">
                <h3><span class="dot"></span> Panduan</h3>
                <ul class="pc-tips">
                    @if ($isCs)
                        <li>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Pastikan nomor telepon WhatsApp aktif dan belum digunakan prospek lain.</span>
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Periksa penanggung jawab marketing agar lead langsung diteruskan ke PIC yang tepat.</span>
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Untuk update status dealing/closing, akan dilakukan oleh tim Marketing terkait.</span>
                        </li>
                    @else
                        <li>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Status <strong>Closing</strong> menandakan deal terjadi. Isi nominal & tanggal closing untuk aktivasi penuh.</span>
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Status <strong>Cancel</strong> untuk prospek yang batal. Memilih cancel akan otomatis menghapus prospek dari sistem.</span>
                        </li>
                        @if ($isMarketing)
                            <li>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Untuk edit data dasar (kontak, layanan, dll), hubungi Customer Service.</span>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </aside>
    </form>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        @if (!$isMarketing)
            flatpickr('.fp-date', {
                dateFormat: 'Y-m-d',
                altInput: false,
                locale: 'id',
                allowInput: true,
                defaultDate: document.querySelector('.fp-date')?.value || null
            });
            flatpickr('.fp-time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
                minuteIncrement: 1,
                locale: 'id',
                allowInput: true,
                defaultDate: document.querySelector('.fp-time')?.value || null
            });

            // Phone input: digits-only, live
            document.querySelectorAll('[data-phone-input]').forEach(function (input) {
                input.addEventListener('input', function () {
                    var cleaned = this.value.replace(/\D+/g, '');
                    if (cleaned !== this.value) this.value = cleaned;
                });
                input.addEventListener('paste', function () {
                    setTimeout(function () {
                        var cleaned = input.value.replace(/\D+/g, '');
                        if (cleaned !== input.value) input.value = cleaned;
                    }, 0);
                });
                input.addEventListener('keydown', function (e) {
                    var allowed = ['Backspace','Delete','Tab','ArrowLeft','ArrowRight','Home','End'];
                    if (allowed.indexOf(e.key) !== -1) return;
                    if (e.ctrlKey || e.metaKey) return;
                    if (!/^[0-9]$/.test(e.key)) e.preventDefault();
                });
            });

            // Realtime phone duplicate check & marketing owner alert (ignore self prospect id)
            (function() {
                var phoneInput = document.getElementById('inputClientPhone');
                var dupAlert = document.getElementById('phone-duplicate-alert');
                var dupMsg = document.getElementById('phone-duplicate-msg');
                var phoneCheckTimer = null;

                function checkPhoneAvailability(phone) {
                    if (!phone || phone.length < 8) {
                        if (dupAlert) dupAlert.style.display = 'none';
                        if (phoneInput && !{{ $errors->has('client_phone') ? 'true' : 'false' }}) {
                            phoneInput.classList.remove('is-invalid');
                        }
                        return;
                    }

                    var url = '{{ route('prospects.check-phone') }}?phone=' + encodeURIComponent(phone) + '&ignore_id={{ $prospect->id }}';
                    fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data && data.exists) {
                            if (phoneInput) phoneInput.classList.add('is-invalid');
                            if (dupMsg) dupMsg.textContent = data.message;
                            if (dupAlert) dupAlert.style.display = 'block';
                        } else {
                            if (dupAlert) dupAlert.style.display = 'none';
                            if (phoneInput && !{{ $errors->has('client_phone') ? 'true' : 'false' }}) {
                                phoneInput.classList.remove('is-invalid');
                            }
                        }
                    })
                    .catch(function() {
                        // Ignore network errors
                    });
                }

                if (phoneInput) {
                    phoneInput.addEventListener('input', function() {
                        clearTimeout(phoneCheckTimer);
                        var val = this.value.trim();
                        phoneCheckTimer = setTimeout(function() {
                            checkPhoneAvailability(val);
                        }, 400);
                    });

                    phoneInput.addEventListener('blur', function() {
                        clearTimeout(phoneCheckTimer);
                        checkPhoneAvailability(this.value.trim());
                    });
                }
            })();
        @endif

        @if (!$isCs)
            flatpickr('.fp-datetime', {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: true,
                minuteIncrement: 1,
                locale: 'id',
                allowInput: true
            });

            function handleProspectSubmit(e) {
                const form = document.getElementById('prospectEditForm');
                const cancelId = '{{ $cancelStatusId }}';
                const selectedStatus = form.querySelector('input[name="status_id"]:checked')?.value;

                if (String(selectedStatus) === String(cancelId)) {
                    if (form.getAttribute('data-swal-confirmed') === 'true') {
                        return true;
                    }
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const confirmTitle = 'Konfirmasi Cancel Prospek';
                    const confirmMsg = 'Apakah Anda yakin prospek ini dicancel? Jika dicancel data akan hilang.';

                    if (window.AppSwal && AppSwal.confirm) {
                        AppSwal.confirm(confirmTitle, confirmMsg, true, 'Ya, Cancel & Hapus').then(function(res) {
                            if (res.isConfirmed) {
                                form.setAttribute('data-swal-confirmed', 'true');
                                form.submit();
                            }
                        });
                    } else if (confirm(confirmMsg)) {
                        form.setAttribute('data-swal-confirmed', 'true');
                        form.submit();
                    }
                    return false;
                }
                return true;
            }

            function formatNominalRupiah(el) {
                let cursor = el.selectionStart;
                let oldLen = el.value.length;
                let digits = el.value.replace(/\D/g, '');
                if (!digits) {
                    el.value = '';
                    return;
                }
                let formatted = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                el.value = formatted;
                let diff = formatted.length - oldLen;
                if (cursor !== null) {
                    let newPos = Math.max(0, cursor + diff);
                    el.setSelectionRange(newPos, newPos);
                }
            }
        @else
            function handleProspectSubmit(e) {
                return true;
            }
        @endif
    </script>
    @endpush
@endsection
