@extends('layouts.app')

@section('title', 'Daftar Prospek — HIVEFIVE')

@section('content')
    <style>
        .prospects-page { display: flex; flex-direction: column; gap: 20px; width: 100%; max-width: 100%; min-width: 0; }

        /* ===== Header banner ===== */
        .prospects-hero {
            position: relative;
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 45%, #3B82F6 100%);
            border-radius: 18px;
            padding: 28px 32px;
            color: #fff;
            overflow: hidden;
            box-shadow: 0 12px 32px rgba(37, 99, 235, .18);
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        .prospects-hero::before {
            content: "";
            position: absolute;
            top: -60px; right: -60px;
            width: 240px; height: 240px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
        }
        .prospects-hero::after {
            content: "";
            position: absolute;
            bottom: -80px; right: 120px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
        }
        .prospects-hero-inner {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .prospects-hero h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .prospects-hero p {
            margin: 6px 0 0;
            color: rgba(255,255,255,.85);
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
            color: rgba(255,255,255,.75);
            font-weight: 600;
            margin-top: 4px;
        }
        .hero-btn {
            background: #fff;
            color: var(--primary-700);
            padding: 11px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 6px 16px rgba(0,0,0,.15);
            transition: transform .18s ease;
        }
        .hero-btn:hover { transform: translateY(-2px); color: var(--primary-700); text-decoration: none; }

        /* ===== Stats strip ===== */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }
        .qstat {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: var(--shadow-card);
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .qstat:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }
        .qstat-icon {
            width: 42px; height: 42px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .qstat-icon.blue   { background: var(--primary-50); color: var(--primary-600); }
        .qstat-icon.green  { background: #ECFDF5; color: #047857; }
        .qstat-icon.amber  { background: #FEF3C7; color: #B45309; }
        .qstat-icon.rose   { background: #FEE2E2; color: #B91C1C; }
        .qstat-icon.purple { background: #FAF5FF; color: #7E22CE; }
        .qstat-val { font-size: 20px; font-weight: 800; letter-spacing: -0.02em; line-height: 1; }
        .qstat-lbl { font-size: 11.5px; color: var(--text-secondary); font-weight: 600; margin-top: 4px; }

        .section-header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 8px;
            margin-bottom: -6px;
            flex-wrap: wrap;
        }
        .section-badge-title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }
        .section-badge-title .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .badge-pill.blue {
            background: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #BFDBFE;
        }
        .badge-pill.purple {
            background: #FAF5FF;
            color: #7E22CE;
            border: 1px solid #E9D5FF;
        }

        /* ===== Filter card ===== */
        .filter-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .filter-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(180deg, #FAFBFC 0%, #FFFFFF 100%);
            gap: 12px;
            flex-wrap: wrap;
        }
        .filter-card-head .ftitle {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        .filter-card-head .ftitle .fic {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: var(--primary-50);
            color: var(--primary-600);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .filter-card-head .fmeta {
            font-size: 12px;
            color: var(--text-muted);
        }
        .filter-form {
            display: grid;
            grid-template-columns: minmax(160px, 1.3fr) minmax(135px, 1fr) minmax(135px, 1fr) minmax(120px, 0.9fr) minmax(135px, 1fr) auto;
            gap: 12px;
            align-items: end;
            padding: 16px 20px;
        }
        .filter-form .field { margin: 0; }
        .filter-form label {
            display: block;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 6px;
        }
        .filter-form input,
        .filter-form select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            font-family: inherit;
            font-size: 13.5px;
            color: var(--text-primary);
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .filter-form input[type="date"] {
            min-height: 42px;
            box-sizing: border-box;
            cursor: pointer;
        }
        .filter-form input:hover,
        .filter-form select:hover { border-color: var(--primary-200); }
        .filter-form input:focus,
        .filter-form select:focus {
            outline: 0;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .filter-search { position: relative; }
        .filter-search .input-wrap {
            position: relative;
        }
        .filter-search svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            transition: color .15s;
        }
        .filter-search input { padding-left: 38px; }
        .filter-search input:focus ~ svg,
        .filter-search input:not(:placeholder-shown) ~ svg { color: var(--primary-600); }

        /* Modern Datepicker Input */
        .date-picker-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .date-picker-wrap input.fp-filter-date {
            width: 100%;
            min-height: 42px;
            padding: 10px 38px 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #FFFFFF;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
            cursor: pointer;
            box-sizing: border-box;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .date-picker-wrap input.fp-filter-date:hover {
            border-color: #93C5FD;
            background: #FAFBFC;
        }
        .date-picker-wrap input.fp-filter-date:focus,
        .date-picker-wrap:focus-within input.fp-filter-date {
            outline: 0;
            border-color: #2563EB;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        .date-picker-wrap .picker-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            pointer-events: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color .15s ease;
        }
        .date-picker-wrap:focus-within .picker-icon {
            color: #2563EB;
        }
        /* Sembunyikan icon native browser agar flatpickr menggantikan sepenuhnya */
        .date-picker-wrap input[type="date"]::-webkit-calendar-picker-indicator {
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

        /* Modern Flatpickr Calendar Theme */
        .flatpickr-calendar {
            background: #FFFFFF !important;
            border-radius: 18px !important;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.16), 0 10px 20px -5px rgba(15, 23, 42, 0.08) !important;
            border: 1px solid #E2E8F0 !important;
            font-family: inherit !important;
            padding: 12px !important;
            width: 320px !important;
            max-width: 95vw !important;
            animation: fpFadeIn .18s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        @keyframes fpFadeIn {
            from { opacity: 0; transform: translateY(-6px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .flatpickr-calendar.arrowTop:before, .flatpickr-calendar.arrowTop:after {
            border-bottom-color: #E2E8F0 !important;
        }
        .flatpickr-calendar.arrowBottom:before, .flatpickr-calendar.arrowBottom:after {
            border-top-color: #E2E8F0 !important;
        }
        .flatpickr-calendar .flatpickr-months {
            padding: 4px 6px 10px !important;
            position: relative;
        }
        .flatpickr-calendar .flatpickr-months .flatpickr-month {
            height: 38px !important;
            color: #1E293B !important;
            fill: #1E293B !important;
        }
        .flatpickr-current-month {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            padding: 4px 0 0 !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 700 !important;
            color: #0F172A !important;
            border-radius: 8px !important;
            padding: 3px 6px !important;
            cursor: pointer;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
            background: #F1F5F9 !important;
        }
        .flatpickr-current-month input.cur-year {
            font-weight: 700 !important;
            color: #0F172A !important;
            border-radius: 8px !important;
            padding: 2px 4px !important;
        }
        .flatpickr-current-month input.cur-year:hover {
            background: #F1F5F9 !important;
        }
        .flatpickr-calendar .flatpickr-months .flatpickr-prev-month,
        .flatpickr-calendar .flatpickr-months .flatpickr-next-month {
            height: 34px !important;
            width: 34px !important;
            padding: 8px !important;
            border-radius: 10px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: background .15s ease, color .15s ease !important;
        }
        .flatpickr-calendar .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-calendar .flatpickr-months .flatpickr-next-month:hover {
            background: #F1F5F9 !important;
            color: #2563EB !important;
        }
        .flatpickr-calendar .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-calendar .flatpickr-months .flatpickr-next-month svg {
            width: 14px !important;
            height: 14px !important;
            fill: #475569 !important;
        }
        .flatpickr-weekdays {
            margin-bottom: 6px !important;
        }
        span.flatpickr-weekday {
            color: #64748B !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: .06em !important;
        }
        .flatpickr-calendar .flatpickr-innerContainer,
        .flatpickr-calendar .flatpickr-days,
        .flatpickr-calendar .dayContainer {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        .flatpickr-calendar .flatpickr-day {
            height: 38px !important;
            line-height: 38px !important;
            max-width: 38px !important;
            border-radius: 10px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            border: 1px solid transparent !important;
            transition: all .12s ease !important;
        }
        .flatpickr-calendar .flatpickr-day:hover {
            background: #EFF6FF !important;
            color: #1D4ED8 !important;
            border-color: #BFDBFE !important;
        }
        .flatpickr-calendar .flatpickr-day.today {
            border-color: #93C5FD !important;
            color: #1D4ED8 !important;
            font-weight: 700 !important;
        }
        .flatpickr-calendar .flatpickr-day.selected,
        .flatpickr-calendar .flatpickr-day.startRange,
        .flatpickr-calendar .flatpickr-day.endRange {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.32) !important;
            border: 0 !important;
        }
        .flatpickr-calendar .flatpickr-day.inRange {
            background: #EFF6FF !important;
            color: #1E40AF !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        .flatpickr-calendar .flatpickr-day.prevMonthDay,
        .flatpickr-calendar .flatpickr-day.nextMonthDay {
            color: #CBD5E1 !important;
        }
        .flatpickr-calendar .flatpickr-day.flatpickr-disabled {
            color: #E2E8F0 !important;
            cursor: not-allowed !important;
        }

        /* Custom select chevron */
        .filter-form .select-wrap {
            position: relative;
        }
        .filter-form .select-wrap::after {
            content: "";
            position: absolute;
            right: 14px;
            top: 50%;
            width: 8px;
            height: 8px;
            border-right: 2px solid var(--text-muted);
            border-bottom: 2px solid var(--text-muted);
            transform: translateY(-70%) rotate(45deg);
            pointer-events: none;
            transition: border-color .15s;
        }
        .filter-form .select-wrap select { padding-right: 36px; appearance: none; -webkit-appearance: none; }
        .filter-form .select-wrap:focus-within::after { border-color: var(--primary-600); }

        .filter-actions {
            display: flex;
            gap: 8px;
            align-items: stretch;
        }
        .filter-actions .btn {
            padding: 10px 16px;
            height: 40px;
            border-radius: 10px;
        }
        .btn-reset {
            background: var(--surface);
            color: var(--text-secondary);
            border: 1px solid var(--border);
            padding: 10px 16px;
            height: 40px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .18s ease;
            text-decoration: none;
        }
        .btn-reset:hover {
            background: #FEE2E2;
            color: #B91C1C;
            border-color: #FECACA;
            text-decoration: none;
        }

        .btn-export-excel {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: linear-gradient(135deg, #10B981, #059669);
            color: #FFFFFF !important;
            padding: 8px 15px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.22);
            transition: all .2s ease;
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
        }
        .btn-export-excel:hover {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 6px 14px rgba(16, 185, 129, 0.32);
            transform: translateY(-1px);
            color: #FFFFFF !important;
            text-decoration: none;
        }
        .btn-export-excel:active {
            transform: translateY(0);
        }

        /* ===== Table card ===== */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        .table-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            gap: 12px;
            flex-wrap: wrap;
        }
        .table-title {
            font-size: 15px;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }
        .table-subtitle {
            font-size: 12.5px;
            color: var(--text-secondary);
            margin: 2px 0 0;
        }

        .mobile-scroll-hint {
            display: none;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            background: #EFF6FF;
            border-bottom: 1px solid #DBEAFE;
            color: #1D4ED8;
            font-size: 11.5px;
            font-weight: 600;
        }
        .mobile-scroll-hint svg {
            flex-shrink: 0;
            animation: hintArrow 1.5s infinite ease-in-out;
        }
        @keyframes hintArrow {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(4px); }
        }

        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-x: contain;
            width: 100%;
            max-width: 100%;
            display: block;
            position: relative;
        }
        .table-wrap::-webkit-scrollbar {
            height: 6px;
        }
        .table-wrap::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        .table-wrap::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 999px;
        }
        .table-wrap::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        .prospect-table {
            width: 100%;
            min-width: 860px;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .prospect-table thead th {
            text-align: left;
            padding: 12px 18px;
            background: #F8FAFC;
            color: var(--text-secondary);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .prospect-table tbody td {
            padding: 14px 18px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: var(--text-primary);
            white-space: nowrap;
        }
        .prospect-table tbody tr {
            transition: background .15s ease, transform .15s ease;
        }
        .prospect-table tbody tr:hover { background: #F8FAFC; }
        .prospect-table tbody tr:last-child td { border-bottom: 0; }

        .cell-date .d { font-weight: 700; color: var(--text-primary); }
        .cell-date .t { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        .cell-client { display: flex; align-items: center; gap: 10px; }
        .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #BFDBFE, #DBEAFE);
            color: var(--primary-700);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }
        .client-info .ph { font-weight: 600; color: var(--text-primary); font-family: 'Inter', monospace; }
        .client-info .sub { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        .svc-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 8px;
            background: #F1F5F9;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
        }
        .svc-chip::before {
            content: "";
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--primary-500);
        }

        .mkt-cell { display: flex; align-items: center; gap: 8px; }
        .mkt-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--accent-green);
            flex-shrink: 0;
        }
        .grp-chip {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            background: #F8FAFC;
            color: var(--text-secondary);
            font-size: 11.5px;
            font-weight: 600;
            border: 1px solid var(--border);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            border: 1px solid transparent;
        }
        .badge::before {
            content: "";
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }
        .badge-open    { background: var(--primary-50); color: var(--primary-600); border-color: var(--primary-200); }
        .badge-closing { background: #ECFDF5; color: #047857; border-color: #A7F3D0; }
        .badge-cancel  { background: #FEE2E2; color: #B91C1C; border-color: #FECACA; }
        .badge-default { background: #F1F5F9; color: #475569; border-color: var(--border); }

        .nominal { font-weight: 700; color: var(--text-primary); font-variant-numeric: tabular-nums; }
        .nominal.empty { color: var(--text-muted); font-weight: 500; }

        .row-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            justify-content: flex-end;
        }
        .icon-btn {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s ease;
            color: var(--text-secondary);
            font-family: inherit;
            text-decoration: none;
        }
        .icon-btn:hover { background: var(--primary-50); color: var(--primary-600); border-color: var(--primary-200); text-decoration: none; }
        .icon-btn.danger:hover { background: #FEE2E2; color: #B91C1C; border-color: #FECACA; }
        .icon-btn.edit:hover { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }

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

        /* ===== Pagination ===== */
        .table-pagination {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            background: #FAFBFC;
        }

        /* ===== Responsive ===== */
        @media (max-width: 1200px) {
            .filter-form { grid-template-columns: 1fr 1fr 1fr; }
            .filter-actions { grid-column: 1 / -1; }
        }
        @media (max-width: 1023px) {
            .quick-stats { grid-template-columns: repeat(3, 1fr); }
            .filter-form { grid-template-columns: 1fr 1fr; }
            .filter-actions { grid-column: 1 / -1; }
        }
        @media (max-width: 720px) {
            .mobile-scroll-hint { display: flex; }
            .prospects-hero { padding: 18px 16px; border-radius: 14px; }
            .prospects-hero h1 { font-size: 20px; }
            .prospects-hero p { font-size: 12.5px; }
            .hero-stats { width: 100%; justify-content: space-between; gap: 12px; margin-top: 6px; }
            .hero-stat .num { font-size: 22px; }
            .hero-stat .lbl { font-size: 10px; }
            .hero-btn { padding: 9px 14px; font-size: 12.5px; }

            .quick-stats { grid-template-columns: repeat(3, 1fr); gap: 8px; }
            .qstat {
                padding: 10px 8px;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 6px;
                border-radius: 12px;
            }
            .qstat-icon { width: 32px; height: 32px; border-radius: 8px; }
            .qstat-icon svg { width: 16px; height: 16px; }
            .qstat-val { font-size: 16px; }
            .qstat-lbl { font-size: 10px; line-height: 1.2; margin-top: 2px; }

            .filter-card { border-radius: 14px; }
            .filter-card-head { padding: 12px 14px; }
            .filter-form { grid-template-columns: 1fr 1fr; padding: 12px 14px; gap: 10px; }
            .filter-search { grid-column: 1 / -1; }
            .filter-actions {
                grid-column: 1 / -1;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-top: 4px;
            }
            .filter-actions .btn,
            .btn-reset {
                width: 100%;
                justify-content: center;
                height: 38px;
                padding: 8px 12px;
                font-size: 13px;
            }

            .table-card { border-radius: 14px; }
            .table-toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                padding: 12px 14px;
            }
            .table-toolbar > div:last-child { width: 100%; }
            .btn-export-excel {
                width: 100%;
                justify-content: center;
                padding: 9px 14px;
                font-size: 12.5px;
            }
            .prospect-table thead th, .prospect-table tbody td { padding: 11px 14px; }
            .icon-btn { width: 34px; height: 34px; }
        }

        .cell-client {
            cursor: pointer;
            transition: opacity .15s ease;
        }
        .cell-client:hover .ph {
            text-decoration: underline;
            color: var(--primary-700);
        }
        .icon-btn.info:hover {
            background: #EFF6FF;
            color: #2563EB;
            border-color: #BFDBFE;
        }

        /* ===== Modal Detail Prospek ===== */
        .p-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .p-modal-overlay.open {
            display: flex;
            animation: pModalFadeIn .2s ease;
        }
        .p-modal-content {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
            border: 1px solid var(--border);
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: pModalSlideUp .25s ease;
        }
        .p-modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);
            flex-shrink: 0;
        }
        .p-modal-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .p-modal-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22);
            flex-shrink: 0;
        }
        .p-modal-title {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .p-modal-sub {
            margin: 3px 0 0;
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .p-modal-close {
            background: #F1F5F9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            color: var(--text-secondary);
            font-size: 20px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s ease;
        }
        .p-modal-close:hover {
            background: #E2E8F0;
            color: var(--text-primary);
        }
        .p-modal-body {
            padding: 22px 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .p-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .p-detail-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 11px 14px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .p-detail-card.span-2 {
            grid-column: span 2;
        }
        .p-detail-label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-muted);
            font-weight: 700;
        }
        .p-detail-value {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-primary);
            word-break: break-word;
        }
        .p-detail-section {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .p-detail-section-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .p-detail-note-text {
            font-size: 13px;
            color: #334155;
            line-height: 1.6;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 12px 14px;
            white-space: pre-wrap;
            margin: 0;
        }
        .p-timeline {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .p-timeline-item {
            display: flex;
            gap: 12px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 10px 14px;
            align-items: flex-start;
        }
        .p-timeline-badge {
            padding: 3px 8px;
            border-radius: 6px;
            background: var(--primary-50);
            color: var(--primary-700);
            font-weight: 700;
            font-size: 11px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .p-timeline-content {
            flex: 1;
            min-width: 0;
        }
        .p-timeline-note {
            font-size: 12.5px;
            color: #1E293B;
            line-height: 1.5;
            white-space: pre-wrap;
            margin-bottom: 4px;
        }
        .p-timeline-meta {
            font-size: 11px;
            color: var(--text-muted);
        }
        .p-modal-footer {
            padding: 14px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FAFBFC;
            flex-shrink: 0;
            gap: 12px;
        }
        .p-modal-btn-close {
            padding: 9px 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all .15s ease;
        }
        .p-modal-btn-close:hover {
            background: #F1F5F9;
            color: var(--text-primary);
        }
        .p-modal-btn-edit {
            padding: 9px 20px;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #FFFFFF !important;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22);
            transition: all .15s ease;
        }
        .p-modal-btn-edit:hover {
            background: linear-gradient(135deg, #1D4ED8, #1E40AF);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.32);
            text-decoration: none;
        }

        @keyframes pModalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes pModalSlideUp {
            from { opacity: 0; transform: translateY(16px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @media (max-width: 600px) {
            .p-detail-grid { grid-template-columns: 1fr; }
            .p-detail-card.span-2 { grid-column: span 1; }
            .p-modal-content { max-height: 95vh; }
        }
    </style>

    <div class="prospects-page">

        {{-- Hero banner --}}
        <div class="prospects-hero">
            <div class="prospects-hero-inner">
                <div>
                    <h1>Daftar Prospek</h1>
                    <p>Kelola dan pantau seluruh prospek dalam satu tampilan.</p>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="num">{{ $totalAll }}</div>
                        <div class="lbl">Total Semua Data</div>
                    </div>
                    @if (in_array($role, ['cs','super_admin']))
                        <a href="{{ route('prospects.create') }}" class="hero-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Prospek Baru
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-card">
            <div class="filter-card-head">
                <h3 class="ftitle">
                    <span class="fic">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    </span>
                    Filter Data
                </h3>
                <span class="fmeta">Saring prospek berdasarkan kriteria tertentu</span>
            </div>
            <form method="GET" action="{{ route('prospects.index') }}" class="filter-form" id="filterProspectsForm">
                <div class="field filter-search">
                    <label>Cari No. HP</label>
                    <div class="input-wrap">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="08xxx...">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                </div>
                <div class="field">
                    <label>Tanggal Awal</label>
                    <div class="date-picker-wrap">
                        <input type="date" name="start_date" id="filterStartDate" class="fp-filter-date" value="{{ request('start_date') }}" placeholder="Pilih tanggal awal..." autocomplete="off">
                        <span class="picker-icon" aria-hidden="true">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="field">
                    <label>Tanggal Akhir</label>
                    <div class="date-picker-wrap">
                        <input type="date" name="end_date" id="filterEndDate" class="fp-filter-date" value="{{ request('end_date') }}" placeholder="Pilih tanggal akhir..." autocomplete="off">
                        <span class="picker-icon" aria-hidden="true">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="field">
                    <label>Status</label>
                    <div class="select-wrap">
                        <select name="status_id">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $s)
                                @if ($s->slug !== 'cancel')
                                    @php
                                        $sLabel = match(strtolower($s->slug)) {
                                            'open' => 'OPEN',
                                            'closing' => 'CLOSE',
                                            default => strtoupper($s->name),
                                        };
                                    @endphp
                                    <option value="{{ $s->id }}" {{ request('status_id') == $s->id ? 'selected' : '' }}>{{ $sLabel }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @if (in_array($role, ['manager_marketing','super_admin']))
                    <div class="field">
                        <label>Marketing</label>
                        <div class="select-wrap">
                            <select name="marketing_user_id">
                                <option value="">Semua Marketing</option>
                                @foreach ($marketings as $m)
                                    <option value="{{ $m->id }}" {{ request('marketing_user_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Terapkan
                    </button>
                    @if (request('q') || request('status_id') || request('marketing_user_id') || request('start_date') || request('end_date') || request('month') || request('year'))
                        <a href="{{ route('prospects.index') }}" class="btn-reset">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Section 1: Non-Iklan (Reguler) --}}
        <div class="section-header-wrap">
            <div class="section-badge-title" style="color: var(--text-primary);">
                <span class="badge-pill blue">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Prospek Reguler (Non-Iklan)
                </span>
                <span style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Data prospek selain grup iklan</span>
            </div>
        </div>

        {{-- Quick stats Non-Iklan --}}
        <div class="quick-stats">
            <div class="qstat">
                <div class="qstat-icon blue">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $nonIklanStats['total'] }}</div>
                    <div class="qstat-lbl">Total Non-Iklan</div>
                </div>
            </div>
            <div class="qstat">
                <div class="qstat-icon blue">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $nonIklanStats['open'] }}</div>
                    <div class="qstat-lbl">Status Open (Non-Iklan)</div>
                </div>
            </div>
            <div class="qstat">
                <div class="qstat-icon green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $nonIklanStats['closing'] }}</div>
                    <div class="qstat-lbl">Closing (Non-Iklan)</div>
                </div>
            </div>
        </div>

        {{-- Table Non-Iklan --}}
        <div class="table-card">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Data Prospek Reguler (Non-Iklan)</h3>
                    <p class="table-subtitle">Menampilkan {{ $prospects->count() }} dari {{ $prospects->total() }} data prospek reguler</p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <a href="{{ route('prospects.export', array_merge(request()->query(), ['group_type' => 'non_iklan'])) }}" class="btn-export-excel" title="Download data prospek non-iklan dalam format Excel sesuai filter">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="8" y1="13" x2="16" y2="13"></line>
                            <line x1="8" y1="17" x2="16" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Export Excel (Non-Iklan)</span>
                    </a>
                </div>
            </div>

            <div class="mobile-scroll-hint">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
                <span>Geser tabel ke samping untuk melihat kolom lengkap</span>
            </div>

            <div class="table-wrap">
                <table class="prospect-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Tanggal</th>
                            <th>Client</th>
                            <th>Layanan</th>
                            <th>Marketing</th>
                            <th>Group</th>
                            <th>Status</th>
                            <th>Nominal</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prospects as $p)
                            @php
                                $initials = strtoupper(substr(preg_replace('/\D/', '', $p->client_phone), 0, 2));
                                if (strlen($initials) < 2) { $initials = strtoupper(substr($p->client_phone, 0, 2)); }
                                $slug = $p->status->slug ?? '';
                                $badgeClass = in_array($slug, ['open','closing','cancel']) ? 'badge-'.$slug : 'badge-default';
                            @endphp
                            <tr>
                                <td style="text-align: center; font-weight: 700; color: var(--text-secondary); font-size: 13px;">
                                    {{ ($prospects->firstItem() ?? 1) + $loop->index }}
                                </td>
                                <td class="cell-date">
                                    <div class="d">{{ $p->entry_date->format('d M Y') }}</div>
                                    <div class="t">{{ $p->entry_time }}</div>
                                </td>
                                <td>
                                    <div class="cell-client" onclick="openProspectDetail({{ $p->id }})" title="Klik untuk lihat detail prospek">
                                        <div class="avatar">{{ $initials }}</div>
                                        <div class="client-info">
                                            <div class="ph">{{ $p->client_phone }}</div>
                                            <div class="sub">{{ $p->source->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="svc-chip">{{ $p->service->name }}</span></td>
                                <td>
                                    <div class="mkt-cell">
                                        <span class="mkt-dot"></span>
                                        <span>{{ $p->marketing->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td><span class="grp-chip">{{ $p->group->name ?? '-' }}</span></td>
                                <td><span class="badge {{ $badgeClass }}">{{ $slug === 'closing' ? 'CLOSE' : $p->status->name }}</span></td>
                                <td>
                                    @if ($p->nominal_closing)
                                        <span class="nominal">Rp {{ number_format($p->nominal_closing, 0, ',', '.') }}</span>
                                    @else
                                        <span class="nominal empty">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <button type="button" class="icon-btn info" title="Lihat Detail Prospek" onclick="openProspectDetail({{ $p->id }})" aria-label="Lihat Detail Prospek">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        @if ($role === 'marketing')
                                            <a href="{{ route('prospects.edit', $p) }}" class="icon-btn" title="Update Prospek" aria-label="Update Prospek">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                            </a>
                                        @else
                                            <a href="{{ route('prospects.edit', $p) }}" class="icon-btn edit" title="Edit Prospek" aria-label="Edit Prospek">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </a>
                                        @endif
                                        @if (in_array($role, ['manager_marketing','super_admin']))
                                            <form method="POST" action="{{ route('prospects.destroy', $p) }}" style="display:inline; margin:0;"
                                                data-confirm="Hapus prospek {{ $p->client_name }}? Data yang dihapus tidak dapat dikembalikan."
                                                data-confirm-title="Hapus Prospek">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="icon-btn danger" title="Hapus" aria-label="Hapus">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="9">
                                    <div class="empty-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div style="font-weight:600; color:var(--text-primary); margin-bottom:4px;">Belum ada data prospek reguler</div>
                                    <div style="font-size:12.5px;">Data prospek selain grup iklan akan muncul di sini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-pagination">
                {{ $prospects->links() }}
            </div>
        </div>

        {{-- Section 2: Khusus Grup Iklan --}}
        <div class="section-header-wrap" style="margin-top: 14px;">
            <div class="section-badge-title" style="color: var(--text-primary);">
                <span class="badge-pill purple">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    Prospek Khusus Grup Iklan
                </span>
                <span style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Data prospek yang terdaftar pada grup Iklan</span>
            </div>
        </div>

        {{-- Quick stats Iklan --}}
        <div class="quick-stats">
            <div class="qstat">
                <div class="qstat-icon purple">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $iklanStats['total'] }}</div>
                    <div class="qstat-lbl">Total Prospek Iklan</div>
                </div>
            </div>
            <div class="qstat">
                <div class="qstat-icon purple">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $iklanStats['open'] }}</div>
                    <div class="qstat-lbl">Status Open (Iklan)</div>
                </div>
            </div>
            <div class="qstat">
                <div class="qstat-icon green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                    <div class="qstat-val">{{ $iklanStats['closing'] }}</div>
                    <div class="qstat-lbl">Closing (Iklan)</div>
                </div>
            </div>
        </div>

        {{-- Table Iklan --}}
        <div class="table-card">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Data Prospek Khusus Grup Iklan</h3>
                    <p class="table-subtitle">Menampilkan {{ $iklanProspects->count() }} dari {{ $iklanProspects->total() }} data prospek iklan</p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <a href="{{ route('prospects.export', array_merge(request()->query(), ['group_type' => 'iklan'])) }}" class="btn-export-excel" style="background: linear-gradient(135deg, #8B5CF6, #6D28D9); box-shadow: 0 4px 10px rgba(139, 92, 246, 0.22);" title="Download data prospek khusus grup iklan dalam format Excel sesuai filter">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="8" y1="13" x2="16" y2="13"></line>
                            <line x1="8" y1="17" x2="16" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Export Excel (Iklan)</span>
                    </a>
                </div>
            </div>

            <div class="mobile-scroll-hint">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
                <span>Geser tabel ke samping untuk melihat kolom lengkap</span>
            </div>

            <div class="table-wrap">
                <table class="prospect-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Tanggal</th>
                            <th>Client</th>
                            <th>Layanan</th>
                            <th>Marketing</th>
                            <th>Group</th>
                            <th>Status</th>
                            <th>Nominal</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($iklanProspects as $p)
                            @php
                                $initials = strtoupper(substr(preg_replace('/\D/', '', $p->client_phone), 0, 2));
                                if (strlen($initials) < 2) { $initials = strtoupper(substr($p->client_phone, 0, 2)); }
                                $slug = $p->status->slug ?? '';
                                $badgeClass = in_array($slug, ['open','closing','cancel']) ? 'badge-'.$slug : 'badge-default';
                            @endphp
                            <tr>
                                <td style="text-align: center; font-weight: 700; color: var(--text-secondary); font-size: 13px;">
                                    {{ ($iklanProspects->firstItem() ?? 1) + $loop->index }}
                                </td>
                                <td class="cell-date">
                                    <div class="d">{{ $p->entry_date->format('d M Y') }}</div>
                                    <div class="t">{{ $p->entry_time }}</div>
                                </td>
                                <td>
                                    <div class="cell-client" onclick="openProspectDetail({{ $p->id }})" title="Klik untuk lihat detail prospek">
                                        <div class="avatar">{{ $initials }}</div>
                                        <div class="client-info">
                                            <div class="ph">{{ $p->client_phone }}</div>
                                            <div class="sub">{{ $p->source->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="svc-chip">{{ $p->service->name }}</span></td>
                                <td>
                                    <div class="mkt-cell">
                                        <span class="mkt-dot"></span>
                                        <span>{{ $p->marketing->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td><span class="grp-chip">{{ $p->group->name ?? '-' }}</span></td>
                                <td><span class="badge {{ $badgeClass }}">{{ $slug === 'closing' ? 'CLOSE' : $p->status->name }}</span></td>
                                <td>
                                    @if ($p->nominal_closing)
                                        <span class="nominal">Rp {{ number_format($p->nominal_closing, 0, ',', '.') }}</span>
                                    @else
                                        <span class="nominal empty">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <button type="button" class="icon-btn info" title="Lihat Detail Prospek" onclick="openProspectDetail({{ $p->id }})" aria-label="Lihat Detail Prospek">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        @if ($role === 'marketing')
                                            <a href="{{ route('prospects.edit', $p) }}" class="icon-btn" title="Update Prospek" aria-label="Update Prospek">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                            </a>
                                        @else
                                            <a href="{{ route('prospects.edit', $p) }}" class="icon-btn edit" title="Edit Prospek" aria-label="Edit Prospek">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </a>
                                        @endif
                                        @if (in_array($role, ['manager_marketing','super_admin']))
                                            <form method="POST" action="{{ route('prospects.destroy', $p) }}" style="display:inline; margin:0;"
                                                data-confirm="Hapus prospek {{ $p->client_name }}? Data yang dihapus tidak dapat dikembalikan."
                                                data-confirm-title="Hapus Prospek">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="icon-btn danger" title="Hapus" aria-label="Hapus">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="9">
                                    <div class="empty-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div style="font-weight:600; color:var(--text-primary); margin-bottom:4px;">Belum ada data prospek iklan</div>
                                    <div style="font-size:12.5px;">Data prospek dengan grup Iklan akan muncul di sini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-pagination">
                {{ $iklanProspects->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Detail Prospek --}}
    <div class="p-modal-overlay" id="prospectDetailModal" role="dialog" aria-modal="true" aria-labelledby="modalDetailPhone">
        <div class="p-modal-content">
            <div class="p-modal-header">
                <div class="p-modal-header-left">
                    <div class="p-modal-avatar" id="modalDetailAvatar">PR</div>
                    <div>
                        <h3 class="p-modal-title">
                            <span id="modalDetailPhone">-</span>
                            <span id="modalDetailStatusBadge" class="badge badge-default">-</span>
                        </h3>
                        <div class="p-modal-sub">
                            <span>Layanan: <strong id="modalDetailSubService" style="color: var(--text-primary);">-</strong></span>
                            <span>&bull;</span>
                            <span>Masuk: <strong id="modalDetailSubDate" style="color: var(--text-primary);">-</strong></span>
                        </div>
                    </div>
                </div>
                <button type="button" class="p-modal-close" onclick="closeProspectDetail()" aria-label="Tutup Modal">&times;</button>
            </div>

            <div class="p-modal-body">
                {{-- Quick action WhatsApp --}}
                <div id="modalWaContainer" style="display: none; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 10px 14px; align-items: center; justify-content: space-between; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: #065F46; font-weight: 600;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669; flex-shrink: 0;"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <span>Hubungi klien via WhatsApp</span>
                    </div>
                    <a id="modalWaBtn" href="#" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; background: #059669; color: #fff; text-decoration: none; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(5,150,105,0.2);">
                        <span>Buka WhatsApp</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </a>
                </div>

                {{-- Grid Detail Informasi --}}
                <div class="p-detail-grid">
                    <div class="p-detail-card">
                        <span class="p-detail-label">Layanan / Jasa</span>
                        <span class="p-detail-value" id="modalDetailService">-</span>
                    </div>
                    <div class="p-detail-card">
                        <span class="p-detail-label">Marketing PIC</span>
                        <span class="p-detail-value" id="modalDetailMarketing">-</span>
                    </div>
                    <div class="p-detail-card">
                        <span class="p-detail-label">Sumber Prospek</span>
                        <span class="p-detail-value" id="modalDetailSource">-</span>
                    </div>
                    <div class="p-detail-card">
                        <span class="p-detail-label">Pengirim Prospek</span>
                        <span class="p-detail-value" id="modalDetailSender">-</span>
                    </div>
                    <div class="p-detail-card">
                        <span class="p-detail-label">Group</span>
                        <span class="p-detail-value" id="modalDetailGroup">-</span>
                    </div>
                    <div class="p-detail-card">
                        <span class="p-detail-label">Tanggal & Jam Masuk</span>
                        <span class="p-detail-value" id="modalDetailEntryDate">-</span>
                    </div>
                    <div class="p-detail-card">
                        <span class="p-detail-label">Nominal Closing</span>
                        <span class="p-detail-value" id="modalDetailNominal" style="color: #047857;">-</span>
                    </div>
                    <div class="p-detail-card">
                        <span class="p-detail-label">Tanggal & Waktu Closing</span>
                        <span class="p-detail-value" id="modalDetailClosedAt">-</span>
                    </div>
                    <div class="p-detail-card span-2">
                        <span class="p-detail-label">Dibuat Oleh</span>
                        <span class="p-detail-value" id="modalDetailCreator" style="font-size: 12.5px; color: var(--text-secondary);">-</span>
                    </div>
                </div>

                {{-- Keterangan Prospek --}}
                <div class="p-detail-section">
                    <h4 class="p-detail-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary-600);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        <span>Keterangan Prospek</span>
                    </h4>
                    <div class="p-detail-note-text" id="modalDetailKeterangan">-</div>
                </div>

                {{-- Catatan Prospek (Todo 7) --}}
                <div class="p-detail-section">
                    <h4 class="p-detail-section-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #047857;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        <span>Catatan Prospek (Perkembangan Todo 7)</span>
                    </h4>
                    <div id="modalDetailCatatanList" class="p-timeline">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <div class="p-modal-footer">
                <button type="button" class="p-modal-btn-close" onclick="closeProspectDetail()">Tutup</button>
                <a href="#" id="modalDetailEditLink" class="p-modal-btn-edit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span>{{ $role === 'marketing' ? 'Update Prospek' : 'Edit Prospek' }}</span>
                </a>
            </div>
        </div>
    </div>

    @php
        $prospectsPayload = [];
        $allCurrentProspects = $prospects->getCollection()->concat($iklanProspects->getCollection());
        foreach ($allCurrentProspects as $p) {
            $cleanPhone = preg_replace('/\D/', '', (string) $p->client_phone);
            if (str_starts_with($cleanPhone, '0')) {
                $waPhone = '62' . substr($cleanPhone, 1);
            } elseif (str_starts_with($cleanPhone, '62')) {
                $waPhone = $cleanPhone;
            } else {
                $waPhone = $cleanPhone ? '62' . $cleanPhone : '';
            }

            $initials = strtoupper(substr(preg_replace('/\D/', '', (string) $p->client_phone), 0, 2));
            if (strlen($initials) < 2) {
                $initials = strtoupper(substr((string) $p->client_phone, 0, 2));
            }

            $slug = $p->status->slug ?? '';
            $badgeClass = in_array($slug, ['open', 'closing', 'cancel']) ? 'badge-' . $slug : 'badge-default';

            $updates = [];
            foreach ($p->weeklyUpdates as $wu) {
                $updates[] = [
                    'month' => (int) $wu->month,
                    'week' => (int) $wu->week_of_month,
                    'note' => (string) $wu->note,
                    'author' => (string) ($wu->user->name ?? '-'),
                    'updated_at' => $wu->updated_at ? $wu->updated_at->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                ];
            }

            $prospectsPayload[$p->id] = [
                'id' => $p->id,
                'client_phone' => $p->client_phone,
                'initials' => $initials,
                'wa_phone' => $waPhone,
                'service_name' => $p->service->name ?? '-',
                'marketing_name' => $p->marketing->name ?? '-',
                'sender_name' => $p->sender->name ?? '-',
                'group_name' => $p->group->name ?? '-',
                'source_name' => $p->source->name ?? '-',
                'status_name' => $slug === 'closing' ? 'CLOSE' : ($p->status->name ?? '-'),
                'status_slug' => $slug,
                'status_badge_class' => $badgeClass,
                'entry_date' => $p->entry_date ? $p->entry_date->translatedFormat('d F Y') : '-',
                'entry_time' => $p->entry_time ?? '-',
                'nominal_closing' => $p->nominal_closing ? 'Rp ' . number_format($p->nominal_closing, 0, ',', '.') : '—',
                'closed_at' => $p->closed_at ? $p->closed_at->translatedFormat('d F Y, H:i') . ' WIB' : '—',
                'keterangan' => $p->note ?: 'Tidak ada keterangan.',
                'creator_name' => ($p->creator->name ?? '-') . ($p->created_at ? ' (' . $p->created_at->translatedFormat('d M Y, H:i') . ' WIB)' : ''),
                'edit_url' => route('prospects.edit', $p),
                'catatan_list' => $updates,
            ];
        }
    @endphp

    <script>
    window.__PROSPECTS__ = {{ Js::from($prospectsPayload) }};

    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function openProspectDetail(id) {
        const p = window.__PROSPECTS__[id];
        if (!p) return;

        document.getElementById('modalDetailAvatar').textContent = p.initials || 'PR';
        document.getElementById('modalDetailPhone').textContent = p.client_phone || '-';

        const statusBadge = document.getElementById('modalDetailStatusBadge');
        statusBadge.textContent = p.status_name;
        statusBadge.className = 'badge ' + p.status_badge_class;

        document.getElementById('modalDetailSubService').textContent = p.service_name;
        document.getElementById('modalDetailSubDate').textContent = p.entry_date + (p.entry_time && p.entry_time !== '-' ? ' ' + p.entry_time : '');

        // WA Button
        const waContainer = document.getElementById('modalWaContainer');
        const waBtn = document.getElementById('modalWaBtn');
        if (p.wa_phone) {
            waBtn.href = 'https://wa.me/' + p.wa_phone;
            waContainer.style.display = 'flex';
        } else {
            waContainer.style.display = 'none';
        }

        // Grid Info
        document.getElementById('modalDetailService').textContent = p.service_name;
        document.getElementById('modalDetailMarketing').textContent = p.marketing_name;
        document.getElementById('modalDetailSource').textContent = p.source_name;
        document.getElementById('modalDetailSender').textContent = p.sender_name;
        document.getElementById('modalDetailGroup').textContent = p.group_name;
        document.getElementById('modalDetailEntryDate').textContent = p.entry_date + ' (' + p.entry_time + ')';
        document.getElementById('modalDetailNominal').textContent = p.nominal_closing;
        document.getElementById('modalDetailClosedAt').textContent = p.closed_at;
        document.getElementById('modalDetailCreator').textContent = p.creator_name;

        // Keterangan
        document.getElementById('modalDetailKeterangan').textContent = p.keterangan;

        // Catatan Prospek (Todo 7)
        const catatanContainer = document.getElementById('modalDetailCatatanList');
        if (p.catatan_list && p.catatan_list.length > 0) {
            catatanContainer.innerHTML = p.catatan_list.map(u => `
                <div class="p-timeline-item">
                    <span class="p-timeline-badge">Minggu ke-${u.week}, ${monthNames[u.month] || ''}</span>
                    <div class="p-timeline-content">
                        <div class="p-timeline-note">${escapeModalHtml(u.note)}</div>
                        <div class="p-timeline-meta">Marketing: ${escapeModalHtml(u.author)} &bull; ${escapeModalHtml(u.updated_at)}</div>
                    </div>
                </div>
            `).join('');
        } else {
            catatanContainer.innerHTML = `
                <div style="text-align: center; padding: 18px 12px; color: var(--text-muted); font-size: 12.5px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 10px;">
                    Belum ada catatan prospek dari Todo 7 untuk klien ini.
                </div>
            `;
        }

        // Edit link
        document.getElementById('modalDetailEditLink').href = p.edit_url;

        // Show modal
        const modal = document.getElementById('prospectDetailModal');
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeProspectDetail() {
        const modal = document.getElementById('prospectDetailModal');
        if (modal) {
            modal.classList.remove('open');
        }
        document.body.style.overflow = '';
    }

    window.openProspectDetail = openProspectDetail;
    window.closeProspectDetail = closeProspectDetail;

    function escapeModalHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProspectDetail();
        }
    });

    const pModalOverlay = document.getElementById('prospectDetailModal');
    if (pModalOverlay) {
        pModalOverlay.addEventListener('click', function(e) {
            if (e.target === pModalOverlay) {
                closeProspectDetail();
            }
        });
    }
    </script>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filterProspectsForm') || document.querySelector('.filter-form');
    const startEl = document.getElementById('filterStartDate');
    const endEl = document.getElementById('filterEndDate');

    let fpStart = null;
    let fpEnd = null;

    function syncDateRequirements() {
        if (!startEl || !endEl) return;
        const startVal = startEl.value ? startEl.value.trim() : '';
        const endVal = endEl.value ? endEl.value.trim() : '';

        startEl.setCustomValidity('');
        endEl.setCustomValidity('');

        if (startVal && !endVal) {
            endEl.setAttribute('required', 'required');
            startEl.removeAttribute('required');
        } else if (!startVal && endVal) {
            startEl.setAttribute('required', 'required');
            endEl.removeAttribute('required');
        } else {
            startEl.removeAttribute('required');
            endEl.removeAttribute('required');
        }
    }

    if (typeof flatpickr !== 'undefined') {
        if (startEl) {
            fpStart = flatpickr(startEl, {
                dateFormat: 'Y-m-d',
                altInput: false,
                locale: 'id',
                allowInput: true,
                disableMobile: "true",
                onChange: function(selectedDates, dateStr) {
                    if (fpEnd) {
                        fpEnd.set('minDate', dateStr || null);
                    }
                    syncDateRequirements();
                },
                onClose: function() {
                    syncDateRequirements();
                }
            });
        }

        if (endEl) {
            fpEnd = flatpickr(endEl, {
                dateFormat: 'Y-m-d',
                altInput: false,
                locale: 'id',
                allowInput: true,
                disableMobile: "true",
                onChange: function(selectedDates, dateStr) {
                    if (fpStart) {
                        fpStart.set('maxDate', dateStr || null);
                    }
                    syncDateRequirements();
                },
                onClose: function() {
                    syncDateRequirements();
                }
            });
        }

        if (startEl && startEl.value && fpEnd) {
            fpEnd.set('minDate', startEl.value);
        }
        if (endEl && endEl.value && fpStart) {
            fpStart.set('maxDate', endEl.value);
        }
    }

    if (startEl) {
        startEl.addEventListener('input', syncDateRequirements);
        startEl.addEventListener('change', syncDateRequirements);
    }
    if (endEl) {
        endEl.addEventListener('input', syncDateRequirements);
        endEl.addEventListener('change', syncDateRequirements);
    }

    syncDateRequirements();

    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            const startVal = startEl ? startEl.value.trim() : '';
            const endVal = endEl ? endEl.value.trim() : '';

            // Jika salah satu dipilih tapi yang lain tidak dipilih, tolak submit filter
            if ((startVal && !endVal) || (!startVal && endVal)) {
                e.preventDefault();
                e.stopPropagation();

                const missingEl = !startVal ? startEl : endEl;
                const missingLabel = !startVal ? 'Tanggal Awal' : 'Tanggal Akhir';
                const filledLabel = !startVal ? 'Tanggal Akhir' : 'Tanggal Awal';
                const msg = `${filledLabel} sudah dipilih. ${missingLabel} wajib dipilih juga untuk melakukan filter.`;

                if (window.AppSwal && AppSwal.fire) {
                    AppSwal.fire({
                        icon: 'warning',
                        title: 'Rentang Tanggal Belum Lengkap',
                        text: msg,
                        confirmButtonText: 'Tutup'
                    });
                } else {
                    alert(msg);
                }

                if (missingEl) {
                    missingEl.focus();
                    if (typeof missingEl.reportValidity === 'function') {
                        missingEl.setCustomValidity(msg);
                        missingEl.reportValidity();
                    }
                }

                return false;
            }

            if (startEl) startEl.setCustomValidity('');
            if (endEl) endEl.setCustomValidity('');
        });
    }
});
</script>
@endpush