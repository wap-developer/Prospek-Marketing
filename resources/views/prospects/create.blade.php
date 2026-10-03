@extends('layouts.app')

@section('title', 'Input Prospek Baru — HIVEFIVE')

@push('styles')
<style>
    .pc-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
        gap: 22px;
        align-items: start;
    }
    @media (max-width: 1023px) { .pc-wrap { grid-template-columns: 1fr; } }

    .pc-form {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 6px 6px 6px;
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
    .pc-field label .req {
        color: #DC2626;
        font-size: 11px;
    }
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
    .pc-input.is-invalid,
    .pc-select.is-invalid {
        border-color: #FCA5A5 !important;
        background: #FEF2F2;
    }
    .pc-input.is-invalid:focus,
    .pc-select.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .12) !important;
        border-color: #DC2626 !important;
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

        /* Date/Time picker — flatpickr */
        .dt-wrap {
            position: relative;
        }
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
        /* Hide native picker indicator */
        .dt-wrap input[type="date"]::-webkit-calendar-picker-indicator,
        .dt-wrap input[type="time"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            cursor: pointer;
        }

        /* flatpickr theme override */
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
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        .flatpickr-calendar .flatpickr-day,
        .flatpickr-calendar .flatpickr-day.flatpickr-disabled,
        .flatpickr-calendar .flatpickr-day.prevMonthDay,
        .flatpickr-calendar .flatpickr-day.nextMonthDay {
            height: 40px !important;
            line-height: 40px !important;
            max-width: 40px !important;
            border-radius: 10px !important;
            font-weight: 600;
            font-size: 13.5px;
        }
        .flatpickr-calendar .flatpickr-day.selected,
        .flatpickr-calendar .flatpickr-day.startRange,
        .flatpickr-calendar .flatpickr-day.endRange {
            background: var(--primary-500) !important;
            border-color: var(--primary-500) !important;
        }
        .flatpickr-calendar .flatpickr-day.inRange {
            background: var(--primary-50) !important;
            box-shadow: none !important;
            color: var(--primary-700) !important;
        }
        .flatpickr-calendar .flatpickr-day:hover {
            background: var(--primary-100) !important;
        }
        .flatpickr-calendar .flatpickr-day.today {
            border-color: var(--primary-200) !important;
        }
        .flatpickr-calendar .flatpickr-day.today.selected {
            border-color: var(--primary-500) !important;
        }
        .flatpickr-calendar .numInputWrapper:hover,
        .flatpickr-calendar .flatpickr-currentMonth .flatpickr-monthDropdown-months:hover {
            background: var(--primary-50) !important;
        }
        .flatpickr-calendar .flatpickr-months .flatpickr-prev-month,
        .flatpickr-calendar .flatpickr-months .flatpickr-next-month {
            padding: 12px !important;
        }
        .flatpickr-calendar .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-calendar .flatpickr-months .flatpickr-next-month svg {
            fill: var(--text-secondary) !important;
            width: 14px;
            height: 14px;
        }
        .flatpickr-time input,
        .flatpickr-time .flatpickr-am-pm {
            font-family: inherit !important;
            font-weight: 700;
            font-size: 15px !important;
            color: var(--text-primary);
        }
        .flatpickr-time {
            border-top: 1px solid var(--border) !important;
            max-height: 56px !important;
            height: 56px !important;
        }
        .flatpickr-time .numInputWrapper {
            height: 56px !important;
        }
        .flatpickr-time .numInputWrapper input {
            font-size: 16px !important;
        }
        span.flatpickr-weekday {
            font-weight: 700;
            color: var(--text-secondary);
            font-size: 12px !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            font-size: 15px !important;
        }
        .flatpickr-months .flatpickr-month {
            height: 44px !important;
        }
    .pc-select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%2364748B' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
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
    .pc-submit-meta {
        font-size: 12px;
        color: var(--text-muted);
    }
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
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pc-card h3 .dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--primary-500);
    }

    .pc-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        font-size: 12.5px;
        border-bottom: 1px dashed var(--border);
        gap: 10px;
    }
    .pc-summary-row:last-child { border-bottom: 0; }
    .pc-summary-row .k { color: var(--text-secondary); font-weight: 500; }
    .pc-summary-row .v { color: var(--text-primary); font-weight: 600; text-align: right; }

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

    @keyframes pcSpin {
        to { transform: rotate(360deg); }
    }
    .pc-spinner {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #fff;
        animation: pcSpin .8s linear infinite;
        display: inline-block;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                <a href="{{ route('prospects.index') }}" style="font-size:12px; color:var(--text-secondary);">Prospek</a>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted)"><polyline points="9 18 15 12 9 6"/></svg>
                <span style="font-size:12px; color:var(--text-primary); font-weight:600;">Input Baru</span>
            </div>
            <h1>Input Prospek Baru</h1>
            <p>Catat data prospek dari klien dan arahkan ke tim marketing yang tepat.</p>
        </div>
        <a href="{{ route('prospects.index') }}" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('prospects.store') }}" class="pc-wrap" novalidate>
        @csrf

        <div class="pc-form">
            {{-- Section 1: Info Klien --}}
            <div class="pc-section">
                <div class="pc-section-head">
                    <span class="pc-num">1</span>
                    <div>
                        <p class="pc-section-title">Informasi Klien</p>
                        <p class="pc-section-sub">Data kontak utama klien yang akan dihubungi.</p>
                    </div>
                </div>

                <div class="pc-grid-2">
                    <div class="pc-field">
                        <label>Nomor Telepon / WhatsApp <span class="req">*</span></label>
                        <input type="text" name="client_phone" id="inputClientPhone" class="pc-input @if($errors->has('client_phone')) is-invalid @endif" value="{{ old('client_phone') }}" required placeholder="08xxx atau 62xxx" maxlength="32" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-phone-input>
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
                        <label>Layanan / Jasa <span class="req">*</span></label>
                        <select name="service_id" class="pc-select" required>
                            <option value="">— Pilih Layanan —</option>
                            @foreach ($services as $s)
                                <option value="{{ $s->id }}" {{ old('service_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <div class="hint">Pilih jenis layanan yang diminati klien.</div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Waktu Masuk --}}
            <div class="pc-section">
                <div class="pc-section-head">
                    <span class="pc-num">2</span>
                    <div>
                        <p class="pc-section-title">Waktu Masuk Prospek</p>
                        <p class="pc-section-sub">Kapan prospek ini diterima / masuk ke sistem.</p>
                    </div>
                </div>

                <div class="pc-grid-2">
                    <div class="pc-field">
                        <label>Tanggal <span class="req">*</span></label>
                        <div class="dt-wrap">
                            <input type="text" name="entry_date" class="pc-input fp-date" value="{{ old('entry_date', date('Y-m-d')) }}" required placeholder="Pilih tanggal" autocomplete="off">
                        </div>
                    </div>
                    <div class="pc-field">
                        <label>Jam <span class="req">*</span></label>
                        <div class="dt-wrap time">
                            <input type="text" name="entry_time" class="pc-input fp-time" value="{{ old('entry_time', date('H:i')) }}" required placeholder="Pilih jam" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Assignment --}}
            <div class="pc-section">
                <div class="pc-section-head">
                    <span class="pc-num">3</span>
                    <div>
                        <p class="pc-section-title">Assignment & Sumber</p>
                        <p class="pc-section-sub">Asal prospek dan siapa yang bertanggung jawab menindaklanjuti.</p>
                    </div>
                </div>

                <div class="pc-grid-2">
                    <div class="pc-field">
                        <label>Pengirim Prospek <span class="req">*</span></label>
                        <select name="sender_id" class="pc-select" required>
                            <option value="">— Pilih Pengirim —</option>
                            @foreach ($senders as $s)
                                <option value="{{ $s->id }}" {{ old('sender_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pc-field">
                        <label>Group <span class="req">*</span></label>
                        <select name="group_id" class="pc-select" required>
                            <option value="">— Pilih Group —</option>
                            @foreach ($groups as $g)
                                <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pc-field">
                        <label>Sumber Prospek <span style="font-size: 11px; font-weight: normal; color: #64748B;">(Opsional)</span></label>
                        <select name="source_id" class="pc-select">
                            <option value="">-</option>
                            @foreach ($sources as $s)
                                @if ($s->name !== '-')
                                    <option value="{{ $s->id }}" {{ old('source_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="pc-field">
                        <label>Marketing Penanggung Jawab <span class="req">*</span></label>
                        <select name="marketing_user_id" class="pc-select" required>
                            <option value="">— Pilih Marketing —</option>
                            @foreach ($marketings as $m)
                                <option value="{{ $m->id }}" {{ old('marketing_user_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="pc-submit">
                <span class="pc-submit-meta">Field bertanda <span style="color:#DC2626;">*</span> wajib diisi.</span>
                <div class="pc-submit-actions">
                    <a href="{{ route('prospects.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" id="btnSubmitProspect" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <span>Simpan Prospek</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="pc-side">
            <div class="pc-card">
                <h3><span class="dot"></span> Ringkasan</h3>
                <div class="pc-summary-row"><span class="k">Tanggal</span><span class="v">{{ date('d M Y') }}</span></div>
                <div class="pc-summary-row"><span class="k">Jam</span><span class="v">{{ date('H:i') }} WIB</span></div>
                <div class="pc-summary-row"><span class="k">Input oleh</span><span class="v">{{ auth()->user()->name }}</span></div>
                <div class="pc-summary-row"><span class="k">Status awal</span><span class="v"><span class="badge badge-open">Open</span></span></div>
            </div>

            <div class="pc-card">
                <h3><span class="dot"></span> Panduan Singkat</h3>
                <ul class="pc-tips">
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Pastikan nomor telepon aktif dan dapat dihubungi via WhatsApp.</span>
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Pilih marketing sesuai group & sumber agar distribusi merata.</span>
                    </li>
                    <li>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Status prospek akan default <strong>Open</strong> dan bisa diupdate oleh marketing.</span>
                    </li>
                </ul>
            </div>
        </aside>
    </form>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        flatpickr('.fp-date', {
            dateFormat: 'Y-m-d',
            altInput: false,
            locale: 'id',
            allowInput: true,
            defaultDate: document.querySelector('.fp-date').value || null
        });
        flatpickr('.fp-time', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 1,
            locale: 'id',
            allowInput: true,
            defaultDate: document.querySelector('.fp-time').value || null
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

        // Realtime phone duplicate check & marketing owner alert
        (function() {
            var phoneInput = document.getElementById('inputClientPhone');
            var dupAlert = document.getElementById('phone-duplicate-alert');
            var dupMsg = document.getElementById('phone-duplicate-msg');
            var phoneCheckTimer = null;
            var isDuplicatePhone = false;

            function checkPhoneAvailability(phone) {
                if (!phone || phone.length < 8) {
                    if (dupAlert) dupAlert.style.display = 'none';
                    if (phoneInput && !{{ $errors->has('client_phone') ? 'true' : 'false' }}) {
                        phoneInput.classList.remove('is-invalid');
                    }
                    isDuplicatePhone = false;
                    return;
                }

                fetch('{{ route('prospects.check-phone') }}?phone=' + encodeURIComponent(phone), {
                    headers: { 'Accept': 'application/json' }
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data && data.exists) {
                        isDuplicatePhone = true;
                        if (phoneInput) phoneInput.classList.add('is-invalid');
                        if (dupMsg) dupMsg.textContent = data.message;
                        if (dupAlert) dupAlert.style.display = 'block';
                    } else {
                        isDuplicatePhone = false;
                        if (dupAlert) dupAlert.style.display = 'none';
                        if (phoneInput && !{{ $errors->has('client_phone') ? 'true' : 'false' }}) {
                            phoneInput.classList.remove('is-invalid');
                        }
                    }
                })
                .catch(function(err) {
                    console.error('Check phone error:', err);
                });
            }

            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    clearTimeout(phoneCheckTimer);
                    var val = this.value.trim();
                    phoneCheckTimer = setTimeout(function() {
                        checkPhoneAvailability(val);
                    }, 350);
                });

                phoneInput.addEventListener('blur', function() {
                    clearTimeout(phoneCheckTimer);
                    checkPhoneAvailability(this.value.trim());
                });

                if (phoneInput.value.trim().length >= 8) {
                    checkPhoneAvailability(phoneInput.value.trim());
                }

                var createForm = phoneInput.closest('form');
                var submitBtn = document.getElementById('btnSubmitProspect');
                var isSubmitting = false;

                if (createForm) {
                    createForm.addEventListener('submit', function(e) {
                        if (isDuplicatePhone) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            var msg = dupMsg ? dupMsg.textContent : 'Nomor prospek sudah ada di sistem.';
                            if (window.AppSwal && AppSwal.fire) {
                                AppSwal.fire({
                                    icon: 'error',
                                    title: 'Nomor Prospek Sudah Terdaftar',
                                    html: '<p style="font-size:13.5px; color:var(--text-secondary); margin:0; line-height:1.5;">' + msg + '</p>',
                                    confirmButtonText: 'Tutup'
                                });
                            } else {
                                alert(msg);
                            }
                            phoneInput.focus();
                            return false;
                        }

                        if (isSubmitting) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            return false;
                        }

                        if (!createForm.checkValidity()) {
                            return;
                        }

                        isSubmitting = true;
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.style.opacity = '0.75';
                            submitBtn.style.cursor = 'not-allowed';
                            submitBtn.innerHTML = '<span class="pc-spinner"></span> <span>Menyimpan...</span>';
                        }
                    });
                }
            }
        })();
    </script>
    @endpush
@endsection
