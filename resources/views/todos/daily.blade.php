@extends('layouts.app')

@section('title', 'To Do Harian — HIVEFIVE')

@push('styles')
<style>
    [x-cloak]{display:none!important;}

    /* ===== Accordion Todos List (Mobile-First) ===== */
    .todos-accordion-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .todo-accordion-card {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        overflow: hidden;
        transition: border-color .18s ease, box-shadow .18s ease;
    }
    .todo-accordion-card.is-open {
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10), 0 8px 24px rgba(37, 99, 235, 0.08);
    }
    .todo-accordion-card.is-done {
        border-color: #A7F3D0;
    }
    .todo-accordion-card.is-done.is-open {
        border-color: #10B981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12), 0 8px 24px rgba(16, 185, 129, 0.08);
    }

    .todo-accordion-trigger {
        width: 100%;
        background: transparent;
        border: 0;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
        cursor: pointer;
        font-family: inherit;
        transition: background .15s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .todo-accordion-trigger:hover {
        background: #F8FAFC;
    }
    .todo-accordion-card.is-open .todo-accordion-trigger {
        background: linear-gradient(135deg, #F0F7FF 0%, #FFFFFF 100%);
        border-bottom: 1px solid var(--border);
    }
    .todo-accordion-card.is-done.is-open .todo-accordion-trigger {
        background: linear-gradient(135deg, #F0FDF4 0%, #FFFFFF 100%);
        border-bottom: 1px solid #D1FAE5;
    }

    .todo-acc-num {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: var(--background);
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all .18s ease;
    }
    .todo-accordion-card.is-open .todo-acc-num {
        background: var(--primary-500);
        color: #fff;
    }
    .todo-accordion-card.is-done .todo-acc-num {
        background: #ECFDF5;
        color: #047857;
    }
    .todo-accordion-card.is-done.is-open .todo-acc-num {
        background: #10B981;
        color: #fff;
    }

    .todo-acc-main {
        flex: 1;
        min-width: 0;
    }
    .todo-acc-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.35;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .todo-accordion-card.is-open .todo-acc-title {
        color: var(--primary-700);
    }
    .todo-acc-sub {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 3px;
        line-height: 1.3;
    }

    .todo-acc-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
    }
    .todo-acc-badge.done {
        background: #ECFDF5;
        color: #047857;
        border: 1px solid #A7F3D0;
    }
    .todo-acc-badge.empty {
        background: var(--background);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }

    .todo-acc-chevron {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--background);
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform .2s ease, background .2s ease, color .2s ease;
    }
    .todo-accordion-card.is-open .todo-acc-chevron {
        transform: rotate(180deg);
        background: var(--primary-50);
        color: var(--primary-600);
    }

    .todo-accordion-content {
        background: var(--surface);
    }

    /* ===== Form card ===== */
    .td-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .td-card-head {
        padding: 22px 24px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }
    .td-card-head .ico {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: var(--primary-50);
        color: var(--primary-600);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .td-card-head h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px;
    }
    .td-card-head p {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.5;
    }
    .td-card-body { padding: 22px 24px; }

    /* ===== Task 1: Platform list (1 col, each platform as a row) ===== */
    .platform-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .platform-card {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        background: var(--surface);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .platform-card:focus-within {
        border-color: var(--primary-200);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .08);
    }
    .platform-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .platform-ico {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .pi-ig  { background: linear-gradient(135deg,#F58529,#DD2A7B,#8134AF); }
    .pi-tt  { background: #000; }
    .pi-fb  { background: #1877F2; }
    .pi-sv  { background: linear-gradient(135deg,#FF7E5F,#FEB47B); color:#7C2D12; }

    .platform-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .platform-count {
        margin-left: auto;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        background: var(--background);
        padding: 3px 8px;
        border-radius: 999px;
    }
    .platform-count.done { color: var(--accent-green); background: #ECFDF5; }

    .link-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    @media (max-width: 720px) { .link-grid { grid-template-columns: 1fr; } }

    .link-row {
        position: relative;
    }
    .link-row .slot {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px; height: 18px;
        border-radius: 5px;
        background: var(--background);
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    .link-row.filled .slot { background: var(--primary-500); color: #fff; }
    .link-row input {
        width: 100%;
        padding: 10px 12px 10px 36px;
        border: 1px solid var(--border);
        border-radius: 9px;
        font-family: inherit;
        font-size: 13px;
        color: var(--text-primary);
        background: var(--surface);
        transition: all .15s ease;
    }
    .link-row input:hover { border-color: #CBD5E1; }
    .link-row input:focus {
        outline: 0;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }
    .link-row.filled input { border-color: #A7F3D0; background: #F0FDF4; }

    /* ===== Task 2-6: Drop zone + files ===== */
    .upload-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .drop-zone {
        position: relative;
        border: 2px dashed #CBD5E1;
        border-radius: 16px;
        padding: 36px 24px;
        text-align: center;
        background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);
        transition: all .2s ease;
        cursor: pointer;
    }
    .drop-zone:hover {
        border-color: var(--primary-500);
        background: linear-gradient(180deg, var(--primary-50) 0%, #FFFFFF 100%);
    }
    .drop-zone.is-locked-zone {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
    .drop-zone:hover .drop-zone-ico {
        background: var(--primary-500);
        color: #fff;
        transform: translateY(-2px);
    }
    .drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .drop-zone-ico {
        width: 56px; height: 56px;
        margin: 0 auto 14px;
        border-radius: 14px;
        background: var(--primary-50);
        color: var(--primary-600);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
    }
    .drop-zone-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px;
    }
    .drop-zone-sub {
        font-size: 12.5px;
        color: var(--text-secondary);
        margin: 0;
    }
    .drop-zone-sub strong { color: var(--primary-600); font-weight: 700; }

    .upload-meta {
        display: flex;
        gap: 14px;
        justify-content: center;
        margin-top: 12px;
        font-size: 11.5px;
        color: var(--text-muted);
    }
    .upload-meta span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .upload-meta svg { color: var(--text-muted); }

    .files-list { margin-top: 4px; }
    .files-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 0 2px;
    }
    .files-head h4 {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: .06em;
        margin: 0;
    }
    .files-head .count-pill {
        background: var(--primary-50);
        color: var(--primary-700);
        font-size: 11px;
        font-weight: 700;
        padding: 2px 10px;
        border-radius: 999px;
    }

    .file-chip {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 8px;
        background: var(--surface);
        transition: all .15s ease;
    }
    .file-chip:hover {
        border-color: var(--primary-200);
        box-shadow: 0 4px 12px rgba(15,23,42,.05);
        transform: translateX(2px);
    }
    .file-chip:last-child { margin-bottom: 0; }
    .file-chip-ico {
        width: 36px; height: 36px;
        border-radius: 9px;
        background: linear-gradient(135deg, #FEE2E2, #FECACA);
        color: #B91C1C;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: .02em;
    }
    .file-chip-ico.is-img {
        background: linear-gradient(135deg, #DBEAFE, #BFDBFE);
        color: #1D4ED8;
    }
    .file-chip-info { flex: 1; min-width: 0; }
    .file-chip-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
    }
    .file-chip-meta {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .file-chip-meta .dot { width: 3px; height: 3px; border-radius: 50%; background: var(--text-muted); }
    .file-chip-actions { display: flex; gap: 4px; }
    .chip-btn {
        width: 30px; height: 30px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-secondary);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .15s ease;
    }
    .chip-btn:hover { color: var(--primary-600); border-color: var(--primary-200); background: var(--primary-50); }
    .chip-btn.del:hover { color: #B91C1C; border-color: #FCA5A5; background: #FEF2F2; }

    .files-empty {
        text-align: center;
        padding: 28px 16px;
        color: var(--text-muted);
        font-size: 12.5px;
        background: var(--background);
        border: 1px dashed var(--border);
        border-radius: 12px;
    }
    .files-empty svg { display: block; margin: 0 auto 8px; opacity: .55; }

    /* ===== Selected files preview (before submit) ===== */
    .selected-list {
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .selected-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: var(--primary-50);
        border: 1px solid var(--primary-200);
        border-radius: 10px;
        font-size: 12.5px;
        animation: slideIn .25s ease;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .selected-row .sico {
        width: 22px; height: 22px;
        border-radius: 6px;
        background: var(--primary-500);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .selected-row .sname {
        flex: 1;
        min-width: 0;
        color: var(--text-primary);
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .selected-row .ssize {
        color: var(--primary-700);
        font-weight: 600;
        font-size: 11.5px;
        font-variant-numeric: tabular-nums;
    }

    /* ===== Upload progress overlay ===== */
    .upload-progress {
        margin-top: 14px;
        padding: 14px 16px;
        background: linear-gradient(135deg, var(--primary-50), #FFFFFF);
        border: 1px solid var(--primary-200);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(37,99,235,.08);
    }
    .upload-progress-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 12.5px;
    }
    .upload-progress-head .label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary-700);
        font-weight: 700;
    }
    .upload-progress-head .spinner {
        width: 14px; height: 14px;
        border: 2px solid var(--primary-200);
        border-top-color: var(--primary-500);
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .upload-progress-head .pct {
        color: var(--primary-700);
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        font-size: 13px;
    }
    .progress-bar {
        height: 10px;
        background: var(--primary-100);
        border-radius: 999px;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 1px 2px rgba(15,23,42,.06);
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-500), var(--primary-600), var(--primary-500));
        background-size: 200% 100%;
        border-radius: 999px;
        width: 0%;
        transition: width .2s ease;
        position: relative;
        overflow: hidden;
        animation: bgSlide 1.6s linear infinite;
        box-shadow: 0 0 10px rgba(37, 99, 235, .35);
    }
    @keyframes bgSlide {
        from { background-position: 0% 0; }
        to   { background-position: 200% 0; }
    }
    .progress-bar-fill::after {
        content: "";
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 40%;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.55) 50%, transparent 100%);
        animation: shimmer 1.1s linear infinite;
    }
    @keyframes shimmer {
        from { transform: translateX(-250%); }
        to   { transform: translateX(350%); }
    }
    .upload-progress {
        animation: progressIn .3s ease;
    }
    @keyframes progressIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .upload-progress.done { background: linear-gradient(135deg, #ECFDF5, #FFFFFF); border-color: #A7F3D0; }
    .upload-progress.done .upload-progress-head { color: #047857; }
    .upload-progress.done .upload-progress-head .label { color: #047857; }
    .upload-progress.done .upload-progress-head .spinner { display: none; }
    .upload-progress.done .progress-bar-fill { background: linear-gradient(90deg, #10B981, #059669); }
    .upload-progress.done .upload-progress-head .pct { color: #047857; }
    .upload-progress.done .check {
        width: 16px; height: 16px;
        border-radius: 50%;
        background: #10B981;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .upload-progress.error { background: linear-gradient(135deg, #FEF2F2, #FFFFFF); border-color: #FCA5A5; }
    .upload-progress.error .upload-progress-head { color: #B91C1C; }
    .upload-progress.error .upload-progress-head .label { color: #B91C1C; }
    .upload-progress.error .upload-progress-head .spinner { display: none; }
    .upload-progress.error .progress-bar-fill { background: #EF4444; }

    .upload-progress .file-being-uploaded {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 6px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ===== Per-task submit bar (inline at bottom of each card) ===== */
    .td-task-submit {
        padding: 16px 24px;
        background: linear-gradient(to top, #F8FAFC, #FFFFFF);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .td-task-submit-meta {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ===== Date picker inline ===== */
    .date-inline {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: var(--background);
        border: 1px solid var(--border);
        border-radius: 10px;
    }
    .date-inline svg { color: var(--text-secondary); }
    .date-inline input {
        border: 0;
        background: transparent;
        font-family: inherit;
        font-size: 13px;
        color: var(--text-primary);
        font-weight: 600;
        outline: 0;
    }
    .date-inline button {
        background: var(--primary-500);
        color: #fff;
        border: 0;
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    /* ===== Modern Refined Compact Table & Toolbar for Task 7 ===== */
    .p-banner {
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 18px;
        transition: all .2s ease;
    }
    .p-banner-completed {
        background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
        border: 1.5px solid #86EFAC;
        box-shadow: 0 2px 8px rgba(16, 185, 129, .06);
    }
    .p-banner-pending {
        background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
        border: 1.5px solid #FDE68A;
        box-shadow: 0 2px 8px rgba(245, 158, 11, .06);
    }
    .p-banner-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .p-banner-title {
        margin: 0;
        font-size: 13.5px;
        font-weight: 800;
    }
    .p-banner-sub {
        margin: 3px 0 0;
        font-size: 12px;
        line-height: 1.4;
    }
    .p-banner-counter {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 10px;
        border: 1px solid;
    }
    .p-progress-track {
        height: 8px;
        background: rgba(0,0,0,0.07);
        border-radius: 999px;
        overflow: hidden;
    }
    .p-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width .3s ease;
    }

    .p-ctrl-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
        padding: 10px 14px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
    }
    .p-tabs {
        display: inline-flex;
        align-items: center;
        background: #EDF2F7;
        padding: 3px;
        border-radius: 10px;
        gap: 3px;
        flex-wrap: wrap;
    }
    .p-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: 0;
        background: transparent;
        color: #64748B;
        transition: all .15s ease;
    }
    .p-tab-btn:hover {
        color: #0F172A;
    }
    .p-tab-btn.active-pending {
        background: #FFFFFF;
        color: #B45309;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .p-tab-btn.active-updated {
        background: #FFFFFF;
        color: #047857;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .p-tab-btn.active-all {
        background: #FFFFFF;
        color: var(--primary-700);
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .p-tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 800;
        min-width: 18px;
        height: 18px;
        line-height: 1;
    }
    .badge-amber { background: #FEF3C7; color: #92400E; }
    .badge-green { background: #D1FAE5; color: #065F46; }
    .badge-slate { background: #E2E8F0; color: #475569; }

    .p-search-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        min-width: 250px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .p-search-box:focus-within {
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }
    .p-search-box input {
        border: 0;
        outline: 0;
        background: transparent;
        font-family: inherit;
        font-size: 12.5px;
        color: var(--text-primary);
        width: 100%;
    }

    .p-table-wrap {
        overflow-x: auto;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        background: #FFFFFF;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .p-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        text-align: left;
    }
    .p-table th {
        background: #F8FAFC;
        padding: 12px 14px;
        font-weight: 800;
        color: #475569;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        border-bottom: 1.5px solid #E2E8F0;
        white-space: nowrap;
    }
    .p-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: top;
    }
    .p-table tr:last-child td {
        border-bottom: 0;
    }
    .p-table tr:hover td {
        background: #F8FAFC;
    }
    .p-table tr.is-just-saved td {
        background: #F0FDF4 !important;
    }

    .p-num-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: #F1F5F9;
        color: #475569;
        font-weight: 800;
        font-size: 11px;
    }

    .p-wa-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 7px;
        border-radius: 6px;
        background: #25D366;
        color: #ffffff !important;
        font-size: 10.5px;
        font-weight: 700;
        text-decoration: none;
        line-height: 1.3;
        transition: transform .12s ease, filter .12s ease;
    }
    .p-wa-btn:hover {
        filter: brightness(0.92);
        transform: translateY(-1px);
    }

    .p-service-badge {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 6px;
        background: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #DBEAFE;
        font-size: 11.5px;
        font-weight: 700;
        line-height: 1.3;
        max-width: 170px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .p-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .p-status-badge.is-done {
        background: #ECFDF5;
        color: #065F46;
        border: 1px solid #A7F3D0;
    }
    .p-status-badge.is-done .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #10B981;
    }
    .p-status-badge.is-pending {
        background: #FFFBEB;
        color: #92400E;
        border: 1px solid #FDE68A;
    }
    .p-status-badge.is-pending .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #F59E0B;
    }

    .p-note-textarea {
        width: 100%;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1.5px solid #CBD5E1;
        font-family: inherit;
        font-size: 12px;
        line-height: 1.4;
        color: #1E293B;
        background: #FFFFFF;
        resize: vertical;
        min-height: 46px;
        box-sizing: border-box;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .p-note-textarea:focus {
        border-color: var(--primary-500);
        outline: 0;
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }

    .p-btn-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        border: 1px solid var(--primary-600);
        background: var(--primary-600);
        color: #ffffff;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(37,99,235,.2);
        transition: all .15s ease;
        white-space: nowrap;
        min-width: 82px;
    }
    .p-btn-save * {
        pointer-events: none;
    }
    .p-btn-save:hover:not(:disabled) {
        background: var(--primary-700);
        border-color: var(--primary-700);
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(37,99,235,.25);
    }
    .p-btn-save:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .p-btn-save.is-saved {
        background: #10B981;
        border-color: #10B981;
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(16,185,129,.2);
    }

    .p-spinner {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid currentColor;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin .6s linear infinite;
    }

    .p-pagination-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #E2E8F0;
    }
    .p-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
    }
    .p-page-btn:hover:not(:disabled) {
        background: var(--primary-50);
        color: var(--primary-700);
        border-color: var(--primary-200);
    }
    .p-page-btn.active {
        background: var(--primary-600);
        color: #fff;
        border-color: var(--primary-600);
        box-shadow: 0 1px 3px rgba(37,99,235,.25);
    }
    .p-page-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }
    .p-page-btn svg {
        width: 15px;
        height: 15px;
        display: block;
        stroke: currentColor;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
    @php
        $platformIcons = [
            'instagram'   => ['IG',  'pi-ig'],
            'tiktok'      => ['TT',  'pi-tt'],
            'facebook'    => ['FB',  'pi-fb'],
            'snack_video' => ['OV',  'pi-sv'],
        ];
        $filledLinks = 0;
        foreach ($platforms as $p) {
            for ($s = 1; $s <= 3; $s++) {
                if (!empty(trim($linksByKey[$p['key']][$s]->url ?? ''))) $filledLinks++;
            }
        }
        $task1Complete = $filledLinks === 12;
    @endphp

    <div class="page-header">
        <div>
            <h1>To Do Harian</h1>
            @if ($showTodo)
                <p>Catat 12 link konten, 5 laporan bukti (PDF / Gambar), dan catatan progres prospek untuk {{ $date->translatedFormat('l, d F Y') }}.</p>
            @else
                <p>Pilih tanggal untuk mulai atau lihat todolist harian.</p>
            @endif
        </div>
        @if ($showTodo)
            <a href="{{ route('todos.daily') }}" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Kalender
            </a>
        @endif
    </div>

    @if (isset($lockSetting) && $lockSetting->is_locked)
        <div class="todo-lock-alert" style="margin-bottom: 20px; background: linear-gradient(135deg, #FEF2F2 0%, #FFF1F2 100%); border: 1.5px solid #FCA5A5; border-radius: 14px; padding: 16px 18px; display: flex; align-items: flex-start; gap: 14px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);">
            <div style="width: 38px; height: 38px; border-radius: 10px; background: #EF4444; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="font-size: 14.5px; font-weight: 700; color: #991B1B; margin-bottom: 3px;">
                    To Do Harian Sedang Dikunci (Masa Rekap)
                </div>
                <div style="font-size: 13px; color: #B91C1C; line-height: 1.45;">
                    {{ $lockSetting->reason ?: 'To Do harian sedang direkap oleh Manager Marketing. Silakan tunggu sampai to-do harian diaktifkan lagi oleh Manager Marketing.' }}
                </div>
                <div style="font-size: 11.5px; color: #DC2626; margin-top: 6px; font-weight: 600;">
                    🔒 Pengisian dan penghapusan To Do sementara dinonaktifkan sampai dibuka kembali oleh Manager Marketing.
                </div>
            </div>
        </div>
    @endif

@if (!$showTodo)
    {{-- ===== KALENDER VIEW ===== --}}
    @php
        $dowLabels = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
        $filledThisMonth = collect($monthStatus)->where('state', 'ok')->count();
        $partialThisMonth = collect($monthStatus)->where('state', 'partial')->count();
    @endphp

    <style>
        .cal-wrap { max-width: 1100px; margin: 0 auto; }
        .cal-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .cal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 24px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, #EFF6FF 0%, #FFFFFF 60%);
            flex-wrap: wrap;
            gap: 12px;
        }
        .cal-month {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .cal-month .m-ic {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: var(--primary-500);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .cal-month .m-name {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }
        .cal-month .m-year {
            font-size: 12.5px;
            color: var(--text-secondary);
            margin-top: 2px;
        }
        .cal-nav { display: flex; gap: 8px; }
        .cal-nav .nav-btn {
            width: 36px; height: 36px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s ease;
        }
        .cal-nav .nav-btn:hover { background: var(--primary-50); color: var(--primary-600); border-color: var(--primary-200); }
        .cal-stats {
            display: flex;
            gap: 8px;
        }
        .cal-stat {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
        }
        .cal-stat .d { width: 8px; height: 8px; border-radius: 50%; }
        .cal-stat.ok .d { background: #10B981; }
        .cal-stat.partial .d { background: #F59E0B; }
        .cal-stat.empty .d { background: #CBD5E1; }
        .cal-stat b { color: var(--text-primary); font-weight: 800; }

        .cal-dow {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            padding: 14px 24px 8px;
            gap: 6px;
        }
        .cal-dow span {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .06em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
            padding: 0 24px 24px;
        }
        .cal-day {
            position: relative;
            min-width: 0;
            aspect-ratio: 1 / 1;
            min-height: 70px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: space-between;
            padding: 10px 8px 8px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            overflow: hidden;
            box-sizing: border-box;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
        }
        .cal-day:hover {
            transform: translateY(-2px);
            border-color: var(--primary-200);
            box-shadow: 0 6px 16px rgba(37,99,235,.10);
        }
        .cal-day.outside { opacity: .35; justify-content: center; align-items: center; }
        .cal-day.future { cursor: not-allowed; opacity: .55; justify-content: center; align-items: center; }
        .cal-day.future:hover { transform: none; border-color: var(--border); box-shadow: none; }
        .cal-day.today {
            border-color: var(--primary-500);
            background: linear-gradient(135deg, var(--primary-50) 0%, #FFFFFF 80%);
            box-shadow: 0 0 0 3px rgba(37,99,235,.08);
        }
        .cal-day .num {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }
        .cal-day.today .num { color: var(--primary-700); }
        .cal-day .dots {
            display: flex;
            gap: 3px;
            align-items: center;
            justify-content: center;
            height: 6px;
        }
        .cal-day .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #CBD5E1;
        }
        .cal-day.ok .dot { background: #10B981; }
        .cal-day.partial .dot { background: #F59E0B; }
        .cal-day .meta {
            font-size: 10px;
            color: var(--text-muted);
            text-align: center;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
            width: 100%;
        }
        .cal-day.ok .meta { color: #047857; }
        .cal-day.partial .meta { color: #B45309; }

        .meta-full { display: inline; }
        .meta-short { display: none; }

        .cal-legend {
            display: flex;
            gap: 18px;
            justify-content: center;
            padding: 12px 24px 18px;
            font-size: 12px;
            color: var(--text-secondary);
            border-top: 1px dashed var(--border);
            background: #FAFBFC;
            flex-wrap: wrap;
        }
        .cal-legend span { display: inline-flex; align-items: center; gap: 6px; }

        .cal-today-cta {
            display: flex;
            gap: 12px;
            padding: 16px 24px;
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
            color: #fff;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .cal-today-cta .ti {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .cal-today-cta .ti-ic {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: rgba(255,255,255,.18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .cal-today-cta .ti-tt {
            font-size: 14px;
            font-weight: 700;
        }
        .cal-today-cta .ti-st {
            font-size: 12px;
            color: rgba(255,255,255,.85);
            margin-top: 2px;
        }
        .cal-today-cta .btn {
            background: #fff;
            color: var(--primary-700);
            font-weight: 700;
            padding: 10px 18px;
            border-radius: 10px;
        }
        .cal-today-cta .btn:hover { transform: translateY(-1px); color: var(--primary-700); }

        @media (max-width: 720px) {
            .meta-full { display: none; }
            .meta-short { display: inline; }

            .cal-card {
                border-radius: 14px;
            }

            .cal-head {
                padding: 14px 12px;
                gap: 10px;
            }
            .cal-month {
                gap: 8px;
            }
            .cal-month .m-ic {
                width: 34px;
                height: 34px;
                border-radius: 9px;
            }
            .cal-month .m-name {
                font-size: 15px;
            }
            .cal-month .m-year {
                font-size: 11px;
            }
            .cal-nav {
                gap: 4px;
            }
            .cal-nav .nav-btn {
                width: 32px;
                height: 32px;
                border-radius: 8px;
            }
            .cal-stats {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 6px;
            }
            .cal-stat {
                justify-content: center;
                padding: 5px 8px;
                font-size: 11px;
            }

            .cal-today-cta {
                padding: 12px 14px;
                gap: 10px;
            }
            .cal-today-cta .ti {
                gap: 10px;
            }
            .cal-today-cta .ti-ic {
                width: 34px;
                height: 34px;
                border-radius: 8px;
            }
            .cal-today-cta .ti-tt {
                font-size: 13px;
            }
            .cal-today-cta .ti-st {
                font-size: 11.5px;
            }
            .cal-today-cta .btn {
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 9px 12px;
                font-size: 12.5px;
            }

            .cal-dow {
                padding: 10px 8px 6px;
                gap: 4px;
            }
            .cal-dow span {
                font-size: 10px;
            }

            .cal-grid {
                padding: 0 8px 16px;
                gap: 4px;
            }

            .cal-day {
                min-height: 52px;
                padding: 5px 2px 4px;
                border-radius: 8px;
            }
            .cal-day .num {
                font-size: 11.5px;
                text-align: center;
            }
            .cal-day .dots {
                gap: 2px;
                height: 4px;
                margin: 2px 0;
            }
            .cal-day .dot {
                width: 4px;
                height: 4px;
            }
            .cal-day .meta {
                font-size: 8.5px;
                line-height: 1;
            }

            .cal-legend {
                padding: 10px 10px 14px;
                gap: 8px 12px;
                font-size: 11px;
            }
        }

        @media (max-width: 380px) {
            .cal-dow, .cal-grid {
                padding-left: 4px;
                padding-right: 4px;
                gap: 3px;
            }
            .cal-day {
                min-height: 48px;
                padding: 4px 1px 3px;
                border-radius: 6px;
            }
            .cal-day .num {
                font-size: 10.5px;
            }
            .cal-day .meta {
                font-size: 8px;
            }
            .cal-day .dot {
                width: 3.5px;
                height: 3.5px;
            }
        }
    </style>

    <div class="cal-wrap">
        <div class="cal-card">
            <div class="cal-head">
                <div class="cal-month">
                    <span class="m-ic">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/></svg>
                    </span>
                    <div>
                        <div class="m-name">{{ $monthStart->translatedFormat('F') }}</div>
                        <div class="m-year">{{ $monthStart->translatedFormat('Y') }}</div>
                    </div>
                </div>
                <div class="cal-nav">
                    <a class="nav-btn" href="{{ route('todos.daily', ['date' => $monthStart->copy()->subMonth()->toDateString()]) }}" aria-label="Bulan sebelumnya">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <a class="nav-btn" href="{{ route('todos.daily', ['date' => now()->toDateString()]) }}" aria-label="Hari ini" title="Hari ini">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="9"/></svg>
                    </a>
                    <a class="nav-btn" href="{{ route('todos.daily', ['date' => $monthStart->copy()->addMonth()->toDateString()]) }}" aria-label="Bulan berikutnya">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>
                <div class="cal-stats">
                    <span class="cal-stat ok"><span class="d"></span><b>{{ $filledThisMonth }}</b> lengkap</span>
                    <span class="cal-stat partial"><span class="d"></span><b>{{ $partialThisMonth }}</b> sebagian</span>
                </div>
            </div>

            @if (($monthStatus[$todayKey]['state'] ?? 'empty') !== 'ok')
                <div class="cal-today-cta">
                    <div class="ti">
                        <span class="ti-ic">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <div>
                            <div class="ti-tt">To Do hari ini belum lengkap</div>
                            <div class="ti-st">{{ now()->translatedFormat('l, d F Y') }} — {{ $monthStatus[$todayKey]['tasksDone'] ?? 0 }}/{{ $monthStatus[$todayKey]['tasksTotal'] ?? 7 }} tugas selesai</div>
                        </div>
                    </div>
                    <a href="{{ route('todos.daily', ['date' => $todayKey, 'view' => 1]) }}" class="btn">
                        Input Sekarang <span class="arrow">→</span>
                    </a>
                </div>
            @endif

            <div class="cal-dow">
                @foreach ($dowLabels as $d)
                    <span>{{ $d }}</span>
                @endforeach
            </div>

            <div class="cal-grid">
                @php
                    // Pad with leading empty days (start of week = Sun)
                    $leading = $firstDow;
                    $prevMonthEnd = $monthStart->copy()->subDay();
                @endphp
                @for ($i = 0; $i < $leading; $i++)
                    @php $d = $prevMonthEnd->copy()->subDays($leading - 1 - $i); @endphp
                    <div class="cal-day outside">
                        <span class="num">{{ $d->day }}</span>
                    </div>
                @endfor

                @for ($day = 1; $day <= $monthDays; $day++)
                    @php
                        $cell = $monthStart->copy()->day($day);
                        $key = $cell->toDateString();
                        $st = $monthStatus[$key]['state'] ?? 'empty';
                        $isToday = $key === $todayKey;
                        $isFuture = $cell->isFuture() && !$isToday;
                        $linksCount = $monthStatus[$key]['links'] ?? 0;
                        $pdfsCount = $monthStatus[$key]['pdfs'] ?? 0;
                    @endphp
                    @if ($isFuture)
                        <div class="cal-day future" title="Belum waktunya">
                            <span class="num">{{ $day }}</span>
                        </div>
                    @else
                        <a href="{{ route('todos.daily', ['date' => $key, 'view' => 1]) }}" class="cal-day {{ $st }} {{ $isToday ? 'today' : '' }}">
                            <span class="num">{{ $day }}</span>
                            <span class="dots">
                                <span class="dot"></span>
                                <span class="dot"></span>
                                <span class="dot"></span>
                            </span>
                            <span class="meta">
                                @if ($st === 'ok')
                                    <span class="meta-full">✓ Lengkap</span>
                                    <span class="meta-short">✓ 7/7</span>
                                @elseif ($st === 'partial')
                                    {{ $monthStatus[$key]['tasksDone'] ?? 0 }}/{{ $monthStatus[$key]['tasksTotal'] ?? 7 }}
                                @else
                                    &nbsp;
                                @endif
                            </span>
                        </a>
                    @endif
                @endfor

                @php
                    $totalCells = $leading + $monthDays;
                    $trailing = (7 - ($totalCells % 7)) % 7;
                @endphp
                @for ($i = 1; $i <= $trailing; $i++)
                    <div class="cal-day outside">
                        <span class="num">{{ $i }}</span>
                    </div>
                @endfor
            </div>

            <div class="cal-legend">
                <span><span class="dot" style="width:8px; height:8px; border-radius:50%; background:#10B981; display:inline-block;"></span> Lengkap (7/7 tugas)</span>
                <span><span class="dot" style="width:8px; height:8px; border-radius:50%; background:#F59E0B; display:inline-block;"></span> Sebagian</span>
                <span><span class="dot" style="width:8px; height:8px; border-radius:50%; background:#CBD5E1; display:inline-block;"></span> Kosong</span>
                <span><span class="dot" style="width:8px; height:8px; border-radius:50%; background:var(--primary-500); display:inline-block;"></span> Hari ini</span>
            </div>
        </div>
    </div>
@else
    @php
        $initialOpen = (int) request('tab', 1);
        if ($initialOpen < 1 || $initialOpen > 7) $initialOpen = 1;
    @endphp
    <div x-data="{
        openTask: {{ $initialOpen }},
        toggle(task) {
            this.openTask = this.openTask === task ? null : task;
            if (this.openTask) {
                sessionStorage.setItem('todoOpen-{{ $date->toDateString() }}', this.openTask);
            }
        },
        isOpen(task) {
            return this.openTask === task;
        }
    }" x-init="
        const stored = sessionStorage.getItem('todoOpen-{{ $date->toDateString() }}');
        if (stored !== null) {
            const p = parseInt(stored);
            if (p >= 1 && p <= 7) openTask = p;
        }
    ">
        <div class="todos-accordion-list">
            {{-- ================= CARD 1: KONTEN PROMOSI ================= --}}
            <div class="todo-accordion-card {{ $task1Complete ? 'is-done' : '' }}" :class="{ 'is-open': isOpen(1) }">
                <button type="button" class="todo-accordion-trigger" @click="toggle(1)">
                    <span class="todo-acc-num">1</span>
                    <div class="todo-acc-main">
                        <div class="todo-acc-title">
                            <span>Membuat Konten Promosi</span>
                            @if ($task1Complete)
                                <span class="todo-acc-badge done">✓ Lengkap (12/12)</span>
                            @else
                                <span class="todo-acc-badge empty">{{ $filledLinks }}/12 link</span>
                            @endif
                        </div>
                        <div class="todo-acc-sub">Input 3 link promosi untuk IG, TikTok, FB, dan Other Video</div>
                    </div>
                    <span class="todo-acc-chevron" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </span>
                </button>

                <div class="todo-accordion-content" x-show="isOpen(1)" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <form method="POST" action="{{ route('todos.daily.store') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                        <input type="hidden" name="task" value="1">

                        <div class="td-card-body" style="padding: 16px 18px 20px;">
                            <div class="platform-list">
                                @foreach ($platforms as $p)
                                    @php
                                        [$short, $cls] = $platformIcons[$p['key']] ?? [strtoupper(substr($p['key'], 0, 2)), 'pi-tt'];
                                        $filled = 0;
                                        for ($s = 1; $s <= 3; $s++) {
                                            if (!empty(trim($linksByKey[$p['key']][$s]->url ?? ''))) $filled++;
                                        }
                                    @endphp
                                    <div class="platform-card">
                                        <div class="platform-head">
                                            <span class="platform-ico {{ $cls }}">{{ $short }}</span>
                                            <span class="platform-name">{{ $p['label'] }}</span>
                                            <span class="platform-count {{ $filled === 3 ? 'done' : '' }}">{{ $filled }}/3</span>
                                        </div>
                                        <div class="link-grid">
                                            @for ($slot = 1; $slot <= 3; $slot++)
                                                @php
                                                    $val = trim($linksByKey[$p['key']][$slot]->url ?? '');
                                                    $filled = $val !== '';
                                                @endphp
                                                <div class="link-row {{ $filled ? 'filled' : '' }}">
                                                    <span class="slot">{{ $slot }}</span>
                                                    <input type="url" name="links[{{ $p['key'] }}][{{ $slot }}]" value="{{ $val }}" @if($p['key'] !== 'snack_video') placeholder="https://{{ strtolower($p['label']) }}.com/..." @endif {{ (isset($lockSetting) && $lockSetting->is_locked) ? 'disabled' : '' }}>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="td-task-submit" style="padding: 14px 18px;">
                            <div class="td-task-submit-meta">
                                Simpan 12 link konten tugas 1
                            </div>
                            <button type="submit" class="btn btn-primary" style="padding: 10px 18px;" {{ (isset($lockSetting) && $lockSetting->is_locked) ? 'disabled' : '' }}>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                {{ (isset($lockSetting) && $lockSetting->is_locked) ? 'Terkunci' : 'Simpan Tugas 1' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= CARDS 2 - 6: PDF TASKS ================= --}}
            @foreach ($pdfTasks as $task => $label)
                @php
                    $existing = $todo->pdfs->where('task', $task);
                    $hasFiles = $existing->count() > 0;
                    $subDesc = match($task) {
                        2 => 'Upload laporan broadcast & komentar media sosial',
                        3 => 'Upload bukti beriklan di akun Instagram',
                        4 => 'Upload laporan kirim DM brosur ke kontak target',
                        5 => 'Upload laporan follow up & menyapa klien lama',
                        6 => 'Upload materi rencana penjualan / closing',
                        default => 'Upload dokumen laporan to-do'
                    };
                @endphp
                <div class="todo-accordion-card {{ $hasFiles ? 'is-done' : '' }}" :class="{ 'is-open': isOpen({{ $task }}) }">
                    <button type="button" class="todo-accordion-trigger" @click="toggle({{ $task }})">
                        <span class="todo-acc-num">{{ $task }}</span>
                        <div class="todo-acc-main">
                            <div class="todo-acc-title">
                                <span>{{ $label }}</span>
                                @if ($hasFiles)
                                    <span class="todo-acc-badge done">✓ {{ $existing->count() }} file</span>
                                @else
                                    <span class="todo-acc-badge empty">Belum upload</span>
                                @endif
                            </div>
                            <div class="todo-acc-sub">{{ $subDesc }}</div>
                        </div>
                        <span class="todo-acc-chevron" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                    </button>

                    <div class="todo-accordion-content" x-show="isOpen({{ $task }})" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        {{-- Upload Form --}}
                        <form method="POST" action="{{ route('todos.daily.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                            <input type="hidden" name="task" value="{{ $task }}">

                            <div class="td-card-body" style="padding: 16px 18px 18px;">
                                <div class="upload-section">
                                    <label class="drop-zone {{ (isset($lockSetting) && $lockSetting->is_locked) ? 'is-locked-zone' : '' }}" style="padding: 24px 16px;">
                                        <input type="file" name="pdfs[{{ $task }}][]" accept=".pdf,image/*,.jpg,.jpeg,.png,.webp" class="pdf-input" {{ (isset($lockSetting) && $lockSetting->is_locked) ? 'disabled' : '' }}>
                                        <div class="drop-zone-ico" style="width: 44px; height: 44px; margin-bottom: 10px;">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        </div>
                                        <p class="drop-zone-title" style="font-size: 13.5px;">{{ $existing->count() ? 'Ganti File Bukti (PDF / Gambar)' : 'Pilih File Bukti (PDF / Gambar)' }}</p>
                                        <p class="drop-zone-sub" style="font-size: 11.5px;">Ketuk untuk upload file (upload baru akan menggantikan file sebelumnya)</p>
                                        <div class="upload-meta" style="font-size: 11px; margin-top: 8px;">
                                            <span>Format PDF / Gambar (JPG, PNG, WEBP) · Maks 100MB · 1 File per tugas</span>
                                        </div>
                                    </label>

                                    <div class="selected-list" data-selected-list hidden></div>
                                    <div class="upload-progress" data-progress hidden>
                                        <div class="upload-progress-head">
                                            <span class="label">
                                                <span class="spinner"></span>
                                                <span data-progress-label>Mengupload…</span>
                                            </span>
                                            <span class="pct" data-progress-pct>0%</span>
                                        </div>
                                        <div class="progress-bar"><div class="progress-bar-fill" data-progress-fill></div></div>
                                        <div class="file-being-uploaded" data-progress-file></div>
                                    </div>
                                </div>
                            </div>

                            <div class="td-task-submit" style="padding: 14px 18px;">
                                <div class="td-task-submit-meta">
                                    Tugas {{ $task }} tersimpan otomatis
                                </div>
                                <button type="submit" class="btn btn-primary" style="padding: 10px 18px;" {{ (isset($lockSetting) && $lockSetting->is_locked) ? 'disabled' : '' }}>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    {{ (isset($lockSetting) && $lockSetting->is_locked) ? 'Terkunci' : 'Upload Tugas '.$task }}
                                </button>
                            </div>
                        </form>

                        {{-- File List --}}
                        @if ($existing->count())
                            <div class="files-list" style="padding: 0 18px 18px; border-top: 1px dashed var(--border); margin-top: 4px; padding-top: 14px;">
                                <div class="files-head">
                                    <h4>File Terupload (Saat Ini)</h4>
                                    <span class="count-pill">{{ $existing->count() }} file</span>
                                </div>
                                <div style="font-size: 11.5px; color: var(--text-muted); margin-bottom: 8px;">
                                    Mengunggah file baru di atas akan otomatis menggantikan file ini.
                                </div>
                                @foreach ($existing as $pdf)
                                    @php
                                        $ext = strtolower(pathinfo($pdf->original_name ?? $pdf->file_path, PATHINFO_EXTENSION));
                                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                        $badgeText = $isImg ? ($ext === 'jpeg' ? 'JPG' : strtoupper($ext)) : 'PDF';
                                    @endphp
                                    <div class="file-chip" id="file-chip-{{ $pdf->id }}">
                                        <span class="file-chip-ico {{ $isImg ? 'is-img' : '' }}">{{ $badgeText }}</span>
                                        <div class="file-chip-info">
                                            <span class="file-chip-name">{{ $pdf->original_name ?? basename($pdf->file_path) }}</span>
                                            <div class="file-chip-meta">
                                                <span>{{ $pdf->created_at->format('d M Y') }}</span>
                                                <span class="dot"></span>
                                                <span>{{ $pdf->created_at->format('H:i') }} WIB</span>
                                            </div>
                                        </div>
                                        <div class="file-chip-actions">
                                            <a href="{{ Storage::url($pdf->file_path) }}" target="_blank" class="chip-btn" title="Lihat file">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </a>
                                            @if (!(isset($lockSetting) && $lockSetting->is_locked))
                                                <button type="button" class="chip-btn del js-delete-pdf" title="Hapus file"
                                                    data-url="{{ route('todos.daily.pdf.destroy', $pdf) }}"
                                                    data-id="{{ $pdf->id }}"
                                                    data-name="{{ $pdf->original_name ?? basename($pdf->file_path) }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="files-list" style="padding: 0 18px 16px;">
                                <div class="files-empty" style="padding: 16px 12px; font-size: 11.5px;">
                                    Belum ada file bukti yang diupload untuk tugas ini
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- ================= CARD 7: UPDATE PERKEMBANGAN PROSPEK PER PROSPEK (ATURAN 2) ================= --}}
            @php
                $task7Filled = $isWeekFullyCompleted;
            @endphp
            <div class="todo-accordion-card {{ $task7Filled ? 'is-done' : '' }}" :class="{ 'is-open': isOpen(7) }">
                <button type="button" class="todo-accordion-trigger" @click="toggle(7)">
                    <span class="todo-acc-num">7</span>
                    <div class="todo-acc-main">
                        <div class="todo-acc-title">
                            <span>Update Perkembangan Prospek (Per Prospek)</span>
                            @if ($targetCount > 0)
                                @if ($isWeekFullyCompleted)
                                    <span class="todo-acc-badge done">✓ Selesai Lengkap ({{ $completedCountThisWeek }}/{{ $targetCount }})</span>
                                @else
                                    <span class="todo-acc-badge" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                                        {{ $completedCountThisWeek }}/{{ $targetCount }} Terupdate
                                    </span>
                                @endif
                            @else
                                <span class="todo-acc-badge" style="background: #F1F5F9; color: #64748B;">Tidak ada prospek masuk</span>
                            @endif
                        </div>
                        <div class="todo-acc-sub">
                            Tanggal Kirim CS: <strong>{{ $date->translatedFormat('l, d F Y') }}</strong> •
                            Update perkembangan seluruh prospek yang dikirim CS pada tanggal ini agar bernilai 'X'.
                        </div>
                    </div>
                    <span class="todo-acc-chevron" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </span>
                </button>

                <div class="todo-accordion-content" x-show="isOpen(7)" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div x-data="task7Manager({
                        prospects: {{ Js::from($prospectsData) }},
                        targetCount: {{ (int) $targetCount }},
                        completedCount: {{ (int) $completedCountThisWeek }},
                        isWeekFullyCompleted: {{ $isWeekFullyCompleted ? 'true' : 'false' }},
                        weekStartStr: '{{ $weekStart->translatedFormat("d M") }}',
                        weekEndStr: '{{ $weekEnd->translatedFormat("d M Y") }}',
                        monthName: '{{ $date->translatedFormat("F Y") }}',
                        isLocked: {{ (isset($lockSetting) && $lockSetting->is_locked) ? 'true' : 'false' }},
                        dateStr: '{{ $date->toDateString() }}',
                        storeUrl: '{{ route("todos.daily.store") }}',
                        csrfToken: '{{ csrf_token() }}'
                    })">
                        <div class="td-card-body" style="padding: 20px;">
                            {{-- Info Progress Harian Banner --}}
                            <div class="p-banner" :class="isWeekFullyCompleted ? 'p-banner-completed' : 'p-banner-pending'">
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="p-banner-icon" :style="isWeekFullyCompleted ? 'background: #10B981;' : 'background: #F59E0B;'">
                                            <template x-if="isWeekFullyCompleted">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            </template>
                                            <template x-if="!isWeekFullyCompleted">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            </template>
                                        </div>
                                        <div>
                                            <h4 class="p-banner-title" :style="isWeekFullyCompleted ? 'color: #065F46;' : 'color: #92400E;'">
                                                Progress Tugas 7: Update Prospek Masuk (Tanggal {{ $date->translatedFormat('d F Y') }})
                                            </h4>
                                            <p class="p-banner-sub" :style="isWeekFullyCompleted ? 'color: #047857;' : 'color: #B45309;'">
                                                <template x-if="targetCount > 0">
                                                    <span>
                                                        <template x-if="isWeekFullyCompleted">
                                                            <span>🎉 Hebat! Seluruh <strong><span x-text="targetCount"></span> prospek yang dikirim pada tanggal ini</strong> telah diupdate. To Do 7 tanggal ini otomatis bernilai <strong>"X" (Hijau)</strong> di matriks manager.</span>
                                                        </template>
                                                        <template x-if="!isWeekFullyCompleted">
                                                            <span>Telah terupdate <strong><span x-text="completedCount"></span></strong> dari <strong><span x-text="targetCount"></span></strong> prospek tanggal ini. Sisa <strong><span x-text="pendingCount"></span></strong> prospek lagi agar tanggal ini tercentang <strong>"X" (Hijau)</strong>.</span>
                                                        </template>
                                                    </span>
                                                </template>
                                                <template x-if="targetCount === 0">
                                                    <span>Tidak ada prospek yang dikirim oleh CS ke Anda pada tanggal {{ $date->translatedFormat('d F Y') }}.</span>
                                                </template>
                                            </p>
                                        </div>
                                    </div>

                                    <template x-if="targetCount > 0">
                                        <div class="p-banner-counter" :style="isWeekFullyCompleted ? 'background: #D1FAE5; border-color: #A7F3D0; color: #065F46;' : 'background: #FEF3C7; border-color: #FDE68A; color: #92400E;'">
                                            <span style="font-size: 18px; font-weight: 900;" x-text="completedCount"></span>
                                            <span style="font-size: 13px; font-weight: 700; opacity: 0.75;">/ <span x-text="targetCount"></span></span>
                                            <span style="font-size: 11px; font-weight: 800; padding: 2px 6px; border-radius: 6px; background: rgba(0,0,0,0.06);" x-text="progressPct + '%'"></span>
                                        </div>
                                    </template>
                                </div>

                                <template x-if="targetCount > 0">
                                    <div class="p-progress-track">
                                        <div class="p-progress-fill" :style="'width: ' + progressPct + '%; background: ' + (isWeekFullyCompleted ? 'linear-gradient(90deg, #10B981, #059669)' : 'linear-gradient(90deg, #F59E0B, #D97706)')"></div>
                                    </div>
                                </template>
                            </div>

                            <div>
                                {{-- Control Bar: Tabs, Live Search, Rows Per Page --}}
                                <div class="p-ctrl-bar">
                                    {{-- Segmented Tabs --}}
                                    <div class="p-tabs">
                                        <template x-if="dayProspects.length > 0">
                                            <button type="button" class="p-tab-btn"
                                                :class="{ 'active-pending': scope === 'day' }"
                                                @click="setScope('day')"
                                                title="Tampilkan hanya prospek yang dikirim pada tanggal ini">
                                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #2563EB;"></span>
                                                <span>Tanggal Ini</span>
                                                <span class="p-tab-badge badge-slate" x-text="dayProspects.length"></span>
                                            </button>
                                        </template>

                                        <button type="button" class="p-tab-btn"
                                            :class="{ 'active-pending': scope !== 'day' && filter === 'pending' }"
                                            @click="setScope('all'); setFilter('pending')"
                                            title="Tampilkan prospek yang belum diisi perkembangannya">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B;"></span>
                                            <span>Belum Diupdate</span>
                                            <span class="p-tab-badge badge-amber" x-text="pendingCount"></span>
                                        </button>

                                        <button type="button" class="p-tab-btn"
                                            :class="{ 'active-updated': scope !== 'day' && filter === 'updated' }"
                                            @click="setScope('all'); setFilter('updated')"
                                            title="Tampilkan prospek yang sudah tersimpan perkembangannya">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981;"></span>
                                            <span>Sudah Diupdate</span>
                                            <span class="p-tab-badge badge-green" x-text="updatedCount"></span>
                                        </button>

                                        <button type="button" class="p-tab-btn"
                                            :class="{ 'active-all': scope !== 'day' && filter === 'all' }"
                                            @click="setScope('all'); setFilter('all')"
                                            title="Tampilkan semua prospek bulan ini">
                                            <span>Semua Bulan Ini</span>
                                            <span class="p-tab-badge badge-slate" x-text="prospects.length"></span>
                                        </button>
                                    </div>

                                        {{-- Search & Per Page --}}
                                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                            <div class="p-search-box">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #94A3B8; flex-shrink: 0;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                                <input type="text" x-model="search" @input="page = 1" placeholder="Cari No HP / Layanan / Pengirim...">
                                                <button type="button" x-show="search" @click="search = ''; page = 1" style="border: 0; background: transparent; cursor: pointer; color: #94A3B8; padding: 0;" title="Hapus pencarian">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                </button>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: #64748B;">
                                                <span style="font-weight: 600;">Per Hal:</span>
                                                <select x-model.number="perPage" @change="page = 1" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 11.5px; font-weight: 700; color: #334155; background: #FFFFFF; outline: 0; cursor: pointer;">
                                                    <option value="15">15</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Compact Clean Table --}}
                                    <div class="p-table-wrap">
                                        <table class="p-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 48px; text-align: center;">No</th>
                                                    <th style="min-width: 190px;">Kontak & Klien</th>
                                                    <th style="min-width: 140px;">Layanan</th>
                                                    <th style="width: 135px; text-align: center;">Status</th>
                                                    <th style="min-width: 290px;">Catatan Perkembangan Minggu Ini</th>
                                                    <th style="width: 105px; text-align: center;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(p, idx) in paginatedProspects" :key="p.id">
                                                    <tr :class="{ 'is-just-saved': p._justSaved }">
                                                        {{-- No --}}
                                                        <td style="text-align: center;">
                                                            <span class="p-num-badge" x-text="itemIndex(idx)"></span>
                                                        </td>

                                                        {{-- Contact info --}}
                                                        <td>
                                                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                                <span style="font-size: 13px; font-weight: 800; color: #0F172A; font-family: monospace; letter-spacing: 0.02em;" x-text="p.phone"></span>
                                                                <template x-if="p.wa">
                                                                    <a :href="'https://wa.me/' + p.wa" target="_blank" rel="noopener" class="p-wa-btn" title="Chat WhatsApp Klien">
                                                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                                                                        <span>Chat WA</span>
                                                                    </a>
                                                                </template>
                                                            </div>
                                                            <div style="display: flex; align-items: center; gap: 8px; font-size: 11px; color: #64748B; margin-top: 5px; flex-wrap: wrap;">
                                                                <span style="display: inline-flex; align-items: center; gap: 4px;">
                                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #94A3B8;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                                    <span x-text="p.entry_date"></span>
                                                                </span>
                                                                <template x-if="p.sender">
                                                                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                                                                        <span style="color: #CBD5E1;">•</span>
                                                                        <span x-text="p.sender"></span>
                                                                    </span>
                                                                </template>
                                                                <template x-if="p.group">
                                                                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                                                                        <span style="color: #CBD5E1;">•</span>
                                                                        <span style="background: #F1F5F9; color: #475569; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 600;" x-text="p.group"></span>
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </td>

                                                        {{-- Layanan --}}
                                                        <td>
                                                            <span class="p-service-badge" :title="p.service" x-text="p.service"></span>
                                                        </td>

                                                        {{-- Status --}}
                                                        <td style="text-align: center;">
                                                            <template x-if="p.is_updated">
                                                                <span class="p-status-badge is-done">
                                                                    <span class="dot"></span>
                                                                    <span>Sudah Diupdate</span>
                                                                </span>
                                                            </template>
                                                            <template x-if="!p.is_updated">
                                                                <span class="p-status-badge is-pending">
                                                                    <span class="dot"></span>
                                                                    <span>Belum Diupdate</span>
                                                                </span>
                                                            </template>
                                                        </td>

                                                        {{-- Catatan --}}
                                                        <td>
                                                            <div>
                                                                <textarea
                                                                    x-model="p.note"
                                                                    @keydown.ctrl.enter.prevent="saveProspectNote(p)"
                                                                    class="p-note-textarea"
                                                                    rows="2"
                                                                    placeholder="Tulis catatan perkembangan klien..."
                                                                    :disabled="isLocked"></textarea>
                                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; font-size: 10.5px;">
                                                                    <span style="color: #64748B;">Klik <strong>Simpan</strong> atau tekan Ctrl + Enter</span>
                                                                    <template x-if="p.updated_at_human">
                                                                        <span style="color: #059669; font-weight: 600;" x-text="'Diperbarui: ' + p.updated_at_human"></span>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        {{-- Action Button --}}
                                                        <td style="text-align: center;">
                                                            <button type="button"
                                                                @click="saveProspectNote(p)"
                                                                class="p-btn-save"
                                                                :class="{ 'is-saved': p._justSaved }"
                                                                :disabled="isLocked || p._saving"
                                                                title="Simpan catatan prospek ini">
                                                                <span x-show="p._saving" style="display: inline-flex; align-items: center; gap: 4px;">
                                                                    <span class="p-spinner"></span>
                                                                    <span>Menyimpan...</span>
                                                                </span>
                                                                <span x-show="!p._saving && p._justSaved" style="display: inline-flex; align-items: center; gap: 4px;">
                                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                                    <span>Tersimpan</span>
                                                                </span>
                                                                <span x-show="!p._saving && !p._justSaved" style="display: inline-flex; align-items: center; gap: 4px;">
                                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                                                                    <span>Simpan</span>
                                                                </span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>

                                                <template x-if="paginatedProspects.length === 0">
                                                    <tr>
                                                        <td colspan="6" style="text-align: center; padding: 36px 16px; color: #64748B;">
                                                            <div style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 12px; background: #F1F5F9; color: #94A3B8; margin-bottom: 8px;">
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                                            </div>
                                                            <div style="font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                                                                Tidak ada data prospek ditemukan
                                                            </div>
                                                            <div style="font-size: 12px; color: #94A3B8;">
                                                                Tidak ada prospek yang cocok dengan tab status atau pencarian ini.
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Pagination Bar --}}
                                    <div class="p-pagination-bar" x-show="totalPages > 1">
                                        <div style="font-size: 12px; font-weight: 600; color: #64748B;" x-text="pageRangeText"></div>

                                        <div style="display: flex; align-items: center; gap: 4px;">
                                            <button type="button" class="p-page-btn" :disabled="page <= 1" @click="setPage(page - 1)" title="Halaman sebelumnya" aria-label="Halaman sebelumnya">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                            </button>

                                            <template x-for="(pNum, pIdx) in pageNumbers" :key="pIdx">
                                                <span>
                                                    <template x-if="pNum === '...'">
                                                        <span style="padding: 0 4px; color: #94A3B8; font-weight: 700;">...</span>
                                                    </template>
                                                    <template x-if="pNum !== '...'">
                                                        <button type="button" class="p-page-btn" :class="{ 'active': page === pNum }" @click="setPage(pNum)" x-text="pNum"></button>
                                                    </template>
                                                </span>
                                            </template>

                                            <button type="button" class="p-page-btn" :disabled="page >= totalPages" @click="setPage(page + 1)" title="Halaman selanjutnya" aria-label="Halaman selanjutnya">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
window.task7Manager = function (cfg) {
    return {
        prospects: (cfg.prospects || []).map(p => ({
            ...p,
            _saving: false,
            _justSaved: false
        })),
        targetCount: cfg.targetCount || 0,
        completedCount: cfg.completedCount || 0,
        isWeekFullyCompleted: !!cfg.isWeekFullyCompleted,
        weekStartStr: cfg.weekStartStr,
        weekEndStr: cfg.weekEndStr,
        isLocked: !!cfg.isLocked,
        dateStr: cfg.dateStr,
        storeUrl: cfg.storeUrl,
        csrfToken: cfg.csrfToken,

        scope: (cfg.prospects || []).some(p => p.is_current_day) ? 'day' : 'all',
        filter: 'pending', // 'pending' | 'updated' | 'all'
        search: '',
        page: 1,
        perPage: 15,

        init() {
            if (this.scope === 'day' && this.dayProspects.length === 0) {
                this.scope = 'all';
            }
            if (this.pendingCount === 0 && this.prospects.length > 0) {
                this.filter = 'updated';
            }
        },

        get dayProspects() {
            return this.prospects.filter(p => p.is_current_day);
        },

        get pendingCount() {
            const base = this.scope === 'day' ? this.dayProspects : this.prospects;
            return base.filter(p => !p.is_updated).length;
        },

        get updatedCount() {
            const base = this.scope === 'day' ? this.dayProspects : this.prospects;
            return base.filter(p => p.is_updated).length;
        },

        get progressPct() {
            if (this.targetCount <= 0) return 0;
            return Math.min(100, Math.round((this.completedCount / this.targetCount) * 100));
        },

        get filteredProspects() {
            let list = this.scope === 'day' ? this.dayProspects : this.prospects;

            if (this.scope !== 'day') {
                if (this.filter === 'pending') {
                    list = list.filter(p => !p.is_updated || p._justSaved);
                } else if (this.filter === 'updated') {
                    list = list.filter(p => p.is_updated);
                }
            }

            const q = (this.search || '').trim().toLowerCase();
            if (q) {
                list = list.filter(p => {
                    return (p.phone && p.phone.toLowerCase().includes(q))
                        || (p.service && p.service.toLowerCase().includes(q))
                        || (p.sender && p.sender.toLowerCase().includes(q))
                        || (p.group && p.group.toLowerCase().includes(q))
                        || (p.note && p.note.toLowerCase().includes(q));
                });
            }

            return list;
        },

        get totalFiltered() {
            return this.filteredProspects.length;
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.totalFiltered / this.perPage));
        },

        get paginatedProspects() {
            const start = (this.page - 1) * this.perPage;
            return this.filteredProspects.slice(start, start + this.perPage);
        },

        get pageRangeText() {
            if (this.totalFiltered === 0) return '0 data';
            const start = (this.page - 1) * this.perPage + 1;
            const end = Math.min(this.totalFiltered, this.page * this.perPage);
            return `${start} - ${end} dari ${this.totalFiltered} prospek`;
        },

        get pageNumbers() {
            const total = this.totalPages;
            const current = this.page;
            const delta = 2;
            const range = [];
            for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
                range.push(i);
            }
            if (current - delta > 2) range.unshift('...');
            if (current + delta < total - 1) range.push('...');
            range.unshift(1);
            if (total > 1) range.push(total);
            return range;
        },

        setScope(s) {
            this.scope = s;
            this.page = 1;
        },

        setFilter(f) {
            this.filter = f;
            this.page = 1;
        },

        setPage(p) {
            if (p >= 1 && p <= this.totalPages) {
                this.page = p;
            }
        },

        itemIndex(idx) {
            return (this.page - 1) * this.perPage + idx + 1;
        },

        async saveProspectNote(target) {
            const p = typeof target === 'object' && target !== null && target.id
                ? target
                : this.prospects.find(item => item.id === target);
            if (!p || this.isLocked || p._saving) return;
            const pId = p.id;
            const noteVal = (p.note || '').trim();

            p._saving = true;

            const fd = new FormData();
            fd.append('_token', this.csrfToken);
            fd.append('date', this.dateStr);
            fd.append('task', '7');
            fd.append('single_prospect_id', pId);
            fd.append('single_note', noteVal);

            try {
                const res = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    p.is_updated = noteVal !== '';
                    p._justSaved = true;
                    p.updated_at_human = 'Baru saja';
                    if (data.completed_count !== undefined) {
                        this.completedCount = data.completed_count;
                        this.isWeekFullyCompleted = !!data.is_week_completed;
                    } else {
                        this.completedCount = this.updatedCount;
                        this.isWeekFullyCompleted = this.completedCount >= this.targetCount;
                    }
                    setTimeout(() => {
                        p._justSaved = false;
                    }, 2500);

                    if (window.AppToast) {
                        AppToast.fire({
                            icon: 'success',
                            title: 'Catatan prospek ' + (p.phone || '') + ' berhasil disimpan'
                        });
                    }
                } else if (data.error) {
                    if (window.AppSwal) {
                        AppSwal.error('Terkunci', data.error);
                    } else {
                        alert(data.error);
                    }
                }
            } catch (e) {
                if (window.AppSwal) {
                    AppSwal.error('Gagal', 'Terjadi kesalahan saat menyimpan catatan.');
                } else {
                    alert('Gagal menyimpan catatan. Periksa jaringan Anda.');
                }
            } finally {
                p._saving = false;
            }
        }
    };
};

(function () {
    function fmtSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function renderSelected(form, files) {
        const list = form.querySelector('[data-selected-list]');
        if (!list) return;
        if (!files || !files.length) {
            list.hidden = true;
            list.innerHTML = '';
            return;
        }
        list.hidden = false;
        list.innerHTML = '';
        Array.from(files).forEach(f => {
            const row = document.createElement('div');
            row.className = 'selected-row';
            row.innerHTML = `
                <span class="sico">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span class="sname"></span>
                <span class="ssize"></span>
            `;
            row.querySelector('.sname').textContent = f.name;
            row.querySelector('.ssize').textContent = fmtSize(f.size);
            list.appendChild(row);
        });
    }

    function uploadWithProgress(form) {
        const fileInput = form.querySelector('.pdf-input');
        const submitBtn = form.querySelector('button[type="submit"]');
        const progress = form.querySelector('[data-progress]');
        const fill = form.querySelector('[data-progress-fill]');
        const pct = form.querySelector('[data-progress-pct]');
        const label = form.querySelector('[data-progress-label]');
        const fileNameEl = form.querySelector('[data-progress-file]');

        if (!progress) return false;

        const files = Array.from(fileInput.files || []);
        const totalBytes = files.reduce((s, f) => s + f.size, 0);
        const totalMB = (totalBytes / (1024 * 1024)).toFixed(2);

        progress.hidden = false;
        progress.classList.remove('done', 'error');
        if (label) label.textContent = 'Mengupload berkas…';
        if (fileNameEl) fileNameEl.textContent = files.map(f => f.name).join(' • ') + ' (' + totalMB + ' MB)';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner" style="width:14px;height:14px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;display:inline-block;vertical-align:middle;"></span> Mengupload…';
        }

        const xhr = new XMLHttpRequest();
        const fd = new FormData(form);

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                const p = Math.round((e.loaded / e.total) * 100);
                if (fill) fill.style.width = p + '%';
                if (pct) pct.textContent = p + '%';
            }
        });

        xhr.addEventListener('load', function () {
            if (xhr.status >= 200 && xhr.status < 400) {
                if (fill) fill.style.width = '100%';
                if (pct) pct.textContent = '100%';
                progress.classList.add('done');
                if (label) {
                    label.innerHTML = '<span class="check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span> Upload selesai!';
                }
                if (fileNameEl) fileNameEl.textContent = 'Memuat ulang data…';
                // Preserve current open task via sessionStorage before reload
                try {
                    const tabEl = document.querySelector('[x-data]');
                    if (tabEl && tabEl._x_dataStack) {
                        const t = tabEl._x_dataStack[0].openTask;
                        sessionStorage.setItem('todoOpen-' + (new URLSearchParams(window.location.search).get('date') || ''), t);
                    }
                } catch (e) {}
                setTimeout(() => window.location.reload(), 500);
            } else {
                progress.classList.add('error');
                if (label) label.textContent = 'Upload gagal (status ' + xhr.status + '). Coba lagi.';
                if (fileNameEl) {
                    var errSnippet = (xhr.responseText || '').replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 180);
                    fileNameEl.textContent = errSnippet || 'Periksa ukuran file / format PDF / Gambar.';
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalText;
                }
            }
        });

        xhr.addEventListener('error', function () {
            progress.classList.add('error');
            if (label) label.textContent = 'Koneksi gagal. Periksa jaringan Anda.';
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalText;
            }
        });

        xhr.open('POST', form.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'text/html, application/xhtml+xml');
        xhr.send(fd);
        return true;
    }

    window.saveSingleProspectNote = function (pId) {
        const btn = document.getElementById('btn_save_p_' + pId);
        const textarea = document.getElementById('note_input_' + pId);
        const dateStr = "{{ $date->toDateString() }}";
        if (!textarea) return;

        const noteVal = textarea.value.trim();
        const origContent = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-block; width:12px; height:12px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .6s linear infinite;"></span> Menyimpan...';
        }

        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('date', dateStr);
        formData.append('task', '7');
        formData.append('single_prospect_id', pId);
        formData.append('single_note', noteVal);

        fetch("{{ route('todos.daily.store') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '✓ Tersimpan';
                btn.style.background = '#ECFDF5';
                btn.style.color = '#047857';
                btn.style.borderColor = '#A7F3D0';
                setTimeout(() => {
                    window.location.reload();
                }, 400);
            }
        })
        .catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origContent;
                if (window.AppSwal) {
                    AppSwal.error('Gagal', 'Terjadi kesalahan saat menyimpan catatan.');
                } else {
                    alert('Terjadi kesalahan saat menyimpan catatan.');
                }
            }
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(function (form) {
            const fileInput = form.querySelector('.pdf-input');
            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    renderSelected(form, fileInput.files);
                });
            }
            form.addEventListener('submit', function (e) {
                const fileInput = form.querySelector('.pdf-input');
                if (!fileInput || !fileInput.files || !fileInput.files.length) {
                    return; // no files → submit normally
                }
                e.preventDefault();
                uploadWithProgress(form);
            });
        });

        // Delete PDF via fetch (no nested form)
        document.querySelectorAll('.js-delete-pdf').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var url = btn.getAttribute('data-url');
                var id = btn.getAttribute('data-id');
                var name = btn.getAttribute('data-name') || 'file ini';

                AppSwal.confirm('Hapus File Bukti', 'Apakah Anda yakin ingin menghapus file "' + name + '"?', true, 'Ya, Hapus').then(function (res) {
                    if (!res.isConfirmed) return;

                    btn.disabled = true;
                    btn.style.opacity = '.5';

                    var fd = new FormData();
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    fd.append('_method', 'DELETE');

                    fetch(url, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).then(function (r) {
                        if (r.ok) {
                            var chip = document.getElementById('file-chip-' + id);
                            if (chip) {
                                chip.style.transition = 'all .3s ease';
                                chip.style.opacity = '0';
                                chip.style.transform = 'translateX(20px)';
                                setTimeout(function () { window.location.reload(); }, 300);
                            } else {
                                window.location.reload();
                            }
                        } else {
                            btn.disabled = false;
                            btn.style.opacity = '1';
                            AppSwal.error('Gagal Menghapus', 'Gagal menghapus file (status ' + r.status + ').');
                        }
                    }).catch(function () {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        AppSwal.error('Koneksi Gagal', 'Gagal menghubungi server. Periksa jaringan Anda.');
                    });
                });
            });
        });
    });
})();
</script>
@endpush
