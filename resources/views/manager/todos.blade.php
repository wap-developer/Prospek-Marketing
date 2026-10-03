@extends('layouts.app')

@section('title', 'Monitor To Do — Manager Marketing')

@section('content')
<style>
    .todos-mgr-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .month-filter-box {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--surface);
        padding: 8px 14px;
        border-radius: 12px;
        border: 1px solid var(--border);
    }
    .month-filter-box select {
        padding: 6px 10px;
        font-size: 13.5px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: inherit;
        font-weight: 600;
        background: #fff;
    }

    .btn-export-excel {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #10B981, #059669);
        color: #FFFFFF !important;
        padding: 8px 18px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.28);
        transition: all .2s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
    }
    .btn-export-excel:hover {
        background: linear-gradient(135deg, #059669, #047857);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.38);
        transform: translateY(-1px);
        color: #FFFFFF !important;
    }
    .btn-export-excel:active {
        transform: translateY(0);
    }
    .btn-export-excel svg {
        flex-shrink: 0;
    }

    .btn-export-single-mkt {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F8FAFC;
        color: #047857 !important;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid #A7F3D0;
        transition: all .15s ease;
    }
    .btn-export-single-mkt:hover {
        background: #ECFDF5;
        border-color: #10B981;
        color: #065F46 !important;
        transform: translateY(-1px);
    }

    /* Accordion Marketing Cards */
    .mkt-accordion-wrap {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 32px;
    }
    .mkt-accordion-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        transition: box-shadow .2s ease, border-color .2s ease;
    }
    .mkt-accordion-card.open {
        border-color: var(--primary-500);
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
    }
    .mkt-accordion-head {
        padding: 16px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        background: #fff;
        transition: background .15s ease;
    }
    .mkt-accordion-head:hover {
        background: #F8FAFC;
    }
    .mkt-accordion-card.open .mkt-accordion-head {
        background: #F0F7FF;
        border-bottom: 1px solid var(--border);
    }
    .mkt-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .mkt-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-500), #6366F1);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        flex-shrink: 0;
    }
    .mkt-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 3px;
    }
    .mkt-sub {
        font-size: 12.5px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .mkt-chevron {
        width: 20px;
        height: 20px;
        color: var(--text-muted);
        transition: transform .2s ease;
    }
    .mkt-accordion-card.open .mkt-chevron {
        transform: rotate(180deg);
        color: var(--primary-600);
    }

    /* Matrix Table Styling */
    .mkt-accordion-body {
        display: none;
        padding: 18px 20px;
        background: #fff;
    }
    .mkt-accordion-card.open .mkt-accordion-body {
        display: block;
    }
    .matrix-table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid var(--border);
        border-radius: 12px;
        width: 100%;
        position: relative;
    }
    .table-todo-matrix {
        width: max-content;
        min-width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
    }
    .table-todo-matrix th,
    .table-todo-matrix td {
        border-bottom: 1px solid #E2E8F0;
        border-right: 1px solid #E2E8F0;
        text-align: center;
        vertical-align: middle;
        padding: 8px 4px;
        background-clip: padding-box;
    }
    .table-todo-matrix tr th:first-child,
    .table-todo-matrix tr td:first-child {
        border-left: 1px solid #E2E8F0;
    }
    .table-todo-matrix tr:first-child th {
        border-top: 1px solid #E2E8F0;
    }
    .table-todo-matrix th.col-no,
    .table-todo-matrix td.col-no {
        text-align: center;
        padding: 10px 6px;
        width: 44px;
        min-width: 44px;
        max-width: 44px;
        position: sticky;
        left: 0;
        background: #FFFFFF;
        z-index: 10;
        font-weight: 800;
        color: var(--text-secondary);
        box-shadow: 1px 0 3px rgba(0,0,0,0.04);
    }
    .table-todo-matrix th.col-no {
        background: #F8FAFC;
        z-index: 11;
    }
    .table-todo-matrix th.col-uraian,
    .table-todo-matrix td.col-uraian {
        text-align: left;
        padding: 10px 14px;
        width: 250px;
        min-width: 220px;
        max-width: 260px;
        position: sticky;
        left: 44px;
        background: #FFFFFF;
        z-index: 10;
        box-shadow: 2px 0 6px rgba(0,0,0,0.06);
    }
    .table-todo-matrix th.col-uraian {
        background: #F8FAFC;
        z-index: 11;
    }
    .table-todo-matrix th.col-ket,
    .table-todo-matrix td.col-ket {
        text-align: left;
        padding: 10px 14px;
        width: 240px;
        min-width: 200px;
        position: sticky;
        left: 294px;
        background: #FFFFFF;
        z-index: 10;
        box-shadow: 2px 0 6px rgba(0,0,0,0.06);
    }
    .table-todo-matrix th.col-ket {
        background: #F8FAFC;
        z-index: 11;
    }
    .table-todo-matrix th.col-day {
        font-size: 11px;
        font-weight: 700;
        background: #F8FAFC;
        width: 32px;
        min-width: 32px;
        color: var(--text-secondary);
    }
    .table-todo-matrix th.col-day.is-today {
        background: var(--primary-100);
        color: var(--primary-700);
        font-weight: 800;
    }

    /* Cell State Colors: Hijau (Filled) vs Coklat Muda (Empty) */
    .cell-state {
        height: 28px;
        line-height: 28px;
        font-weight: 800;
        font-size: 12px;
        transition: transform .1s ease, filter .15s ease;
    }
    .cell-clickable {
        cursor: pointer;
    }
    .cell-clickable:hover {
        filter: brightness(0.92);
        box-shadow: inset 0 0 0 2px var(--primary-600);
    }
    .cell-state.filled {
        background: #22C55E !important; /* Hijau Segar */
        color: #FFFFFF !important;
    }
    .cell-state.empty {
        background: #D7C4B7 !important; /* Coklat Muda Pastel / Light Warm Brown */
        color: transparent;
    }

    /* Modern Modal Styling */
    .todo-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
        opacity: 0;
        transition: opacity .25s ease;
    }
    .todo-modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .todo-modal-content {
        background: #FFFFFF;
        border-radius: 24px;
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35), 0 0 0 1px rgba(226, 232, 240, 0.8);
        display: flex;
        flex-direction: column;
        transform: scale(0.96);
        transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .todo-modal-overlay.active .todo-modal-content {
        transform: scale(1);
    }
    .todo-modal-header {
        padding: 22px 28px;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
        border-radius: 24px 24px 0 0;
        position: sticky;
        top: 0;
        z-index: 20;
    }
    .modal-hdr-user {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .modal-hdr-avatar {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary-600), #4F46E5);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 17px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .modal-close-btn {
        background: #F1F5F9;
        border: 0;
        cursor: pointer;
        color: #64748B;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .15s ease;
    }
    .modal-close-btn:hover {
        background: #E2E8F0;
        color: #0F172A;
        transform: scale(1.05);
    }
    .todo-modal-body {
        padding: 26px 28px;
        background: #FFFFFF;
    }
    .modal-task-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: #EFF6FF;
        color: var(--primary-700);
        border: 1px solid #BFDBFE;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 800;
        margin-bottom: 18px;
    }
    .modal-link-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 16px 18px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .modal-link-box:focus-within {
        border-color: var(--primary-400);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        background: #FFFFFF;
    }
    .modal-link-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        font-size: 13px;
        background: #FFFFFF;
        transition: all .15s ease;
    }
    .modal-link-input:focus {
        outline: 0;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
    }
    /* Modal Notification Banner */
    .modal-success-banner {
        display: none;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #059669 0%, #10B981 100%);
        color: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.35);
        animation: bannerSlideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes bannerSlideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .modal-success-banner .banner-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .modal-success-banner .banner-text {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .modal-success-banner .banner-text strong {
        font-size: 13.5px;
        font-weight: 800;
        letter-spacing: -0.01em;
    }
    .modal-success-banner .banner-text span {
        font-size: 11.5px;
        opacity: 0.9;
        margin-top: 1px;
    }
    .modal-success-banner .banner-close {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.85);
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .15s ease;
    }
    .modal-success-banner .banner-close:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #FFFFFF;
    }

    /* Modern Dropzone & Upload Cards */
    .current-file-container {
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 16px;
        padding: 14px 18px;
    }
    .empty-file-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px 12px;
        text-align: center;
        border: 1.5px dashed #CBD5E1;
        border-radius: 12px;
        background: #FFFFFF;
    }
    .empty-file-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #F1F5F9;
        color: #94A3B8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }
    .modal-file-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        padding: 12px 16px;
        border-radius: 14px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        transition: all .15s ease;
    }
    .modal-file-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }
    .pdf-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, #FEE2E2, #FECACA);
        color: #DC2626;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 9px;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.12);
    }
    .pdf-icon-badge.is-img {
        background: linear-gradient(135deg, #DBEAFE, #BFDBFE);
        color: #1D4ED8;
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.12);
    }
    .pdf-icon-badge span {
        margin-top: -2px;
        letter-spacing: 0.05em;
    }
    .pdf-filename {
        font-weight: 700;
        font-size: 13.5px;
        color: #0F172A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 400px;
    }
    .pdf-filemeta {
        font-size: 11.5px;
        color: #059669;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
        font-weight: 600;
    }
    .badge-verified-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10B981;
    }
    .badge-new-upload {
        display: inline-block;
        margin-left: 6px;
        padding: 2px 8px;
        background: #10B981;
        color: #fff;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.02em;
    }
    .btn-file-preview {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #EFF6FF;
        color: var(--primary-600);
        border: 1px solid #BFDBFE;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all .15s ease;
    }
    .btn-file-preview:hover {
        background: var(--primary-600);
        color: #fff;
        border-color: var(--primary-600);
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25);
    }
    .btn-file-delete {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 12px;
        background: #FEF2F2;
        color: #DC2626;
        border: 1px solid #FECACA;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
    }
    .btn-file-delete:hover {
        background: #DC2626;
        color: #fff;
        border-color: #DC2626;
        box-shadow: 0 3px 10px rgba(220, 38, 38, 0.25);
    }

    /* Modern Dropzone */
    .modern-dropzone {
        border: 2px dashed #CBD5E1;
        border-radius: 18px;
        padding: 24px 20px;
        background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
        transition: all .2s ease;
        gap: 8px;
        position: relative;
    }
    .modern-dropzone:hover {
        border-color: #2563EB;
        background: #F0F7FF;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.08);
    }
    .modern-dropzone.dragover {
        border-color: #1D4ED8;
        background: #DBEAFE;
        transform: scale(1.01);
    }
    .modern-dropzone.has-file {
        border-color: #10B981;
        background: #F0FDF4;
    }
    .dropzone-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #EFF6FF;
        color: #2563EB;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2px;
        transition: all .2s ease;
    }
    .modern-dropzone:hover .dropzone-icon-circle {
        transform: translateY(-2px);
        background: #2563EB;
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    .dropzone-primary-text {
        font-size: 13.5px;
        color: #1E293B;
        margin: 0;
    }
    .dropzone-primary-text strong {
        color: #2563EB;
        font-weight: 800;
    }
    .dropzone-sub-text {
        font-size: 11.5px;
        color: #64748B;
        margin: 2px 0 0;
    }
    .dropzone-browse-btn {
        margin-top: 4px;
        padding: 6px 16px;
        background: #FFFFFF;
        color: #1E293B;
        border: 1.5px solid #CBD5E1;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: all .15s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .modern-dropzone:hover .dropzone-browse-btn {
        border-color: #2563EB;
        color: #2563EB;
        background: #FFFFFF;
    }

    /* Selected File Preview Card */
    .selected-file-preview-card {
        margin-top: 12px;
        background: #F0FDF4;
        border: 1.5px solid #86EFAC;
        border-radius: 14px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: newCardSlideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
    }
    .selected-file-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .selected-file-badge {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #DC2626;
        color: #fff;
        font-weight: 900;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .selected-file-badge.is-img {
        background: #2563EB;
    }
    .selected-file-name {
        font-size: 13.5px;
        font-weight: 800;
        color: #065F46;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 380px;
    }
    .selected-file-meta {
        font-size: 11.5px;
        color: #047857;
        margin-top: 2px;
    }
    .btn-cancel-selection {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        background: #FFFFFF;
        color: #DC2626;
        border: 1px solid #FECACA;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
    }
    .btn-cancel-selection:hover {
        background: #FEF2F2;
        border-color: #DC2626;
    }

    .btn-open-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        color: var(--primary-600);
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        cursor: pointer;
        transition: all .15s ease;
        text-decoration: none;
    }
    .btn-open-link:hover {
        background: var(--primary-600);
        color: #fff;
        border-color: var(--primary-600);
    }
    @keyframes newCardSlideIn {
        0% { transform: translateY(-12px); opacity: 0; background: #ECFDF5; border-color: #10B981; }
        60% { background: #ECFDF5; border-color: #10B981; }
        100% { transform: translateY(0); opacity: 1; background: #FFFFFF; border-color: #E2E8F0; }
    }
    .modal-file-card-new {
        animation: newCardSlideIn 1.2s cubic-bezier(0.16, 1, 0.3, 1);
        border-color: #10B981 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    .upload-btn-spinner {
        display: inline-block;
        width: 13px;
        height: 13px;
        border: 2px solid #ffffff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-right: 6px;
        vertical-align: middle;
    }

    /* Legend Bar */
    .matrix-legend {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 14px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
    }
    .legend-box {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 10px;
    }
    .legend-box.green {
        background: #22C55E;
        color: #fff;
    }
    .legend-box.brown {
        background: #D7C4B7;
    }
</style>

<div class="todos-mgr-header">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; margin: 0 0 6px; letter-spacing: -0.02em;">Monitor To Do Harian Tim</h1>
        <p style="color: var(--text-secondary); font-size: 14.5px; margin: 0;">
            Rekap matriks kepatuhan 7 to-do harian marketing selama bulan <strong>{{ $monthLabel }}</strong>.
        </p>
    </div>

    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        {{-- Tombol Export Excel ZIP (Background Job) --}}
        <button type="button" onclick="startAsyncExport()" id="btnStartExportZip" class="btn-export-excel" title="Download arsip ZIP berisi file Excel individual per marketing (Background Job)">
            <svg id="btnExportZipIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span id="btnExportZipText">Export Semua (.ZIP)</span>
        </button>

        {{-- Filter Bulan & Tahun --}}
        <form method="GET" action="{{ route('manager.todos') }}" class="month-filter-box">
            <label style="font-size: 12px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Periode:</label>
            <select name="month" onchange="this.form.submit()">
                @for ($m = 1; $m <= 12; $m++)
                    @php $mName = Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F'); @endphp
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $mName }}</option>
                @endfor
            </select>
            <select name="year" onchange="this.form.submit()">
                @for ($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>
</div>

{{-- Banner Live Progress Export Background Job --}}
<div id="exportLiveBanner" style="display: none; margin-bottom: 24px; border-radius: 16px; padding: 18px 22px; box-shadow: var(--shadow-card); transition: all 0.3s ease;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 260px;">
            <div id="exportLiveIconBox" style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 20px;"></div>
            <div style="flex: 1;">
                <div id="exportLiveTitle" style="font-size: 15px; font-weight: 800; color: #0F172A; line-height: 1.3;">Memproses Export...</div>
                <div id="exportLiveSubtitle" style="font-size: 12.5px; color: #475569; margin-top: 2px;">Mengumpulkan data to-do marketing...</div>
            </div>
        </div>
        <div id="exportLiveActionBox" style="display: flex; align-items: center; gap: 10px;"></div>
    </div>

    {{-- Progress bar wrap --}}
    <div id="exportLiveProgressWrap" style="margin-top: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 5px;">
            <span id="exportLiveCountText">0 dari 0 Marketing</span>
            <span id="exportLivePercentText">0%</span>
        </div>
        <div style="width: 100%; height: 8px; background: rgba(0, 0, 0, 0.08); border-radius: 999px; overflow: hidden;">
            <div id="exportLiveProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #3B82F6, #1D4ED8); border-radius: 999px; transition: width 0.25s ease;"></div>
        </div>
        <div style="font-size: 11.5px; color: #64748B; margin-top: 6px; display: flex; align-items: center; gap: 6px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            <span>Diproses di server di latar belakang (Background Job).</span>
        </div>
    </div>
</div>

{{-- Panel Kontrol Kunci/Buka To-Do Harian --}}
@if (isset($lockSetting))
    <div class="card" style="margin-bottom: 24px; border: 1.5px solid {{ $lockSetting->is_locked ? '#FCA5A5' : '#A7F3D0' }}; background: {{ $lockSetting->is_locked ? 'linear-gradient(135deg, #FEF2F2 0%, #FFFFFF 100%)' : 'linear-gradient(135deg, #F0FDF4 0%, #FFFFFF 100%)' }}; border-radius: 16px; padding: 18px 22px; box-shadow: var(--shadow-card);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: flex-start; gap: 14px; max-width: 680px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $lockSetting->is_locked ? '#EF4444' : '#10B981' }}; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    @if ($lockSetting->is_locked)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>
                    @endif
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                        <h2 style="margin: 0; font-size: 15px; font-weight: 800; color: var(--text-primary);">
                            Status To Do Harian:
                        </h2>
                        @if ($lockSetting->is_locked)
                            <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 800; color: #991B1B; background: #FEE2E2; border: 1px solid #FCA5A5; padding: 2px 8px; border-radius: 999px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #EF4444;"></span>
                                TERKUNCI (Masa Rekap)
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 800; color: #065F46; background: #D1FAE5; border: 1px solid #A7F3D0; padding: 2px 8px; border-radius: 999px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981;"></span>
                                TERBUKA (Aktif)
                            </span>
                        @endif
                    </div>
                    <p style="margin: 0; font-size: 12.5px; color: var(--text-secondary); line-height: 1.4;">
                        {{ $lockSetting->is_locked ? ($lockSetting->reason ?: 'To Do harian sedang dikunci untuk rekap.') : 'Tim marketing dapat mengisi tugas harian.' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('manager.todos.lock-toggle') }}">
                @csrf
                @if ($lockSetting->is_locked)
                    <button type="submit" class="btn" style="background: #10B981; color: #fff; border: 0; padding: 9px 18px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                        Buka Kunci To Do
                    </button>
                @else
                    <button type="submit" class="btn" style="background: #EF4444; color: #fff; border: 0; padding: 9px 18px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                        Kunci To Do (Rekap)
                    </button>
                @endif
            </form>
        </div>
    </div>
@endif

{{-- Legend Matriks --}}
<div class="matrix-legend">
    <span>Keterangan Status Sel:</span>
    <span style="display:inline-flex; align-items:center; gap:6px;">
        <span class="legend-box green">X</span> Terisi / Selesai (Hijau)
    </span>
    <span style="display:inline-flex; align-items:center; gap:6px;">
        <span class="legend-box brown"></span> Belum Terisi (Coklat Muda)
    </span>
</div>

{{-- Daftar Marketing (Klik untuk Buka Matriks To-Do 1 Bulan) --}}
<div class="mkt-accordion-wrap">
    @forelse ($matrixData as $index => $row)
        @php
            $m = $row['user'];
            $isOpenDefault = ($index === 0);
        @endphp
        <div class="mkt-accordion-card {{ $isOpenDefault ? 'open' : '' }}" id="card-mkt-{{ $m->id }}">
            <div class="mkt-accordion-head" onclick="toggleMktCard('{{ $m->id }}')">
                <div class="mkt-info">
                    <div class="mkt-avatar">{{ strtoupper(substr($m->name, 0, 1)) }}</div>
                    <div>
                        <div class="mkt-name">{{ $m->name }}</div>
                        <div class="mkt-sub">
                            <span>{{ '@' . $m->username }}</span>
                            <span>•</span>
                            <span style="color:var(--primary-600); font-weight:700;">{{ $row['totalFilledDays'] }} Hari Aktif</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="{{ route('manager.todos.export', ['month' => $month, 'year' => $year, 'user_id' => $m->id]) }}"
                       class="btn-export-single-mkt"
                       onclick="event.stopPropagation();"
                       title="Download Excel khusus {{ $m->name }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <span>Export Excel</span>
                    </a>
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-secondary); background: #F1F5F9; padding: 4px 10px; border-radius: 999px;">
                        Klik untuk melihat detail 1 bulan
                    </span>
                    <svg class="mkt-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>

            <div class="mkt-accordion-body">
                <div class="matrix-table-container">
                    <table class="table-todo-matrix">
                        <thead>
                            <tr>
                                <th class="col-no">No</th>
                                <th class="col-uraian">Uraian Tugas</th>
                                <th class="col-ket">Keterangan</th>
                                <th colspan="{{ $daysInMonth }}" style="background: #F1F5F9; font-weight: 800; font-size: 12px;">
                                    Result Bulan {{ $monthLabel }} (Tanggal 1 s/d {{ $daysInMonth }})
                                </th>
                            </tr>
                            <tr>
                                <th class="col-no">#</th>
                                <th class="col-uraian">Nama Kegiatan</th>
                                <th class="col-ket">Syarat Selesai</th>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $isToday = (now()->month == $month && now()->year == $year && now()->day == $d);
                                    @endphp
                                    <th class="col-day {{ $isToday ? 'is-today' : '' }}" title="Tanggal {{ $d }} {{ $monthLabel }}">
                                        {{ $d }}
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasksList as $tNum => $tMeta)
                                <tr>
                                    <td class="col-no">{{ $tNum }}</td>
                                    <td class="col-uraian">
                                        <strong style="color: var(--text-primary);">{{ $tMeta['title'] }}</strong>
                                    </td>
                                    <td class="col-ket" style="color: var(--text-secondary);">
                                        {{ $tMeta['desc'] }}
                                    </td>

                                    {{-- Kolom Tanggal 1 s/d Hari Terakhir Bulan Ini --}}
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $isFilled = $row['dailyTasks'][$d][$tNum] ?? false;
                                            $cellDateStr = Carbon\Carbon::createFromDate($year, $month, $d)->format('Y-m-d');
                                        @endphp
                                        <td id="matrix_cell_{{ $m->id }}_{{ $tNum }}_{{ $d }}"
                                            class="cell-state {{ $isFilled ? 'filled' : 'empty' }} cell-clickable"
                                            title="Klik untuk lihat/edit to-do {{ $m->name }} tgl {{ $d }} (Tugas {{ $tNum }})"
                                            onclick="openTodoModal({{ $m->id }}, {{ json_encode($m->name) }}, {{ json_encode($cellDateStr) }}, {{ $tNum }}, {{ $d }})">
                                            {{ $isFilled ? 'X' : '' }}
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="text-align: center; padding: 36px 20px; color: var(--text-muted);">
            Belum ada anggota tim marketing terdaftar.
        </div>
    @endforelse
</div>

<script>
    function toggleMktCard(id) {
        const card = document.getElementById('card-mkt-' + id);
        if (card) {
            card.classList.toggle('open');
        }
    }
</script>

{{-- Modal Edit To-Do Harian oleh Manager --}}
<div class="todo-modal-overlay" id="todoEditModal" onclick="if(event.target === this) closeTodoModal()">
    <div class="todo-modal-content">
        <div class="todo-modal-header">
            <div class="modal-hdr-user">
                <div class="modal-hdr-avatar" id="modalAvatar">M</div>
                <div>
                    <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0F172A;" id="modalTitle">
                        Detail & Edit To-Do Marketing
                    </h3>
                    <p style="margin: 3px 0 0; font-size: 13px; color: #64748B; font-weight: 500;" id="modalSubTitle">
                        Memuat tanggal...
                    </p>
                </div>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeTodoModal()" title="Tutup Modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <form id="modalUpdateForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="date" id="modalInputDate">
            <input type="hidden" name="task" id="modalActiveTask" value="1">

            <div class="todo-modal-body">
                {{-- Badge Nama Tugas Aktif yang sedang diedit --}}
                <div class="modal-task-badge" id="modalTaskBadge">
                    📌 Tugas #1: Posting 12 Link Media Sosial
                </div>

                {{-- Alert Sukses Modern di Atas Modal Body --}}
                <div id="modalSuccessToast" class="modal-success-banner">
                    <div class="banner-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div class="banner-text">
                        <strong>Berhasil Disimpan!</strong>
                        <span id="modalSuccessToastMsg">Perubahan to-do berhasil disimpan ke sistem.</span>
                    </div>
                    <button type="button" class="banner-close" onclick="document.getElementById('modalSuccessToast').style.display='none'" title="Tutup">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                {{-- Box Progress Bar Upload Modern --}}
                <div id="modalUploadProgressBox" style="display: none; padding: 14px 18px; background: #F8FAFC; border: 1.5px solid #CBD5E1; border-radius: 14px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 13px; font-weight: 700; color: #1E293B; display: flex; align-items: center; gap: 8px;">
                            <span class="upload-btn-spinner" style="border-color: #2563EB; border-top-color: transparent;"></span>
                            <span id="modalUploadProgressText">Mengunggah berkas ke server...</span>
                        </span>
                        <span id="modalUploadProgressPercent" style="font-size: 13px; font-weight: 800; color: #2563EB;">0%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                        <div id="modalUploadProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #3B82F6, #2563EB); border-radius: 999px; transition: width 0.15s ease;"></div>
                    </div>
                </div>

                {{-- Loader Modern --}}
                <div id="modalLoading" style="text-align: center; padding: 48px 0; color: #64748B;">
                    <div style="display: inline-block; width: 36px; height: 36px; border: 3.5px solid #E2E8F0; border-top-color: var(--primary-600); border-radius: 50%; animation: spin 0.8s linear infinite; margin-bottom: 12px;"></div>
                    <div style="font-weight: 700; font-size: 13.5px; color: #1E293B;">Mengambil data to-do marketing...</div>
                </div>

                <style>
                    @keyframes spin { to { transform: rotate(360deg); } }
                </style>

                {{-- Task 1: 12 Links --}}
                <div id="taskPane1" class="task-pane" style="display: none;">
                    <div style="margin-bottom: 16px;">
                        <h4 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 2px;">Posting 12 Link Media Sosial</h4>
                        <p style="font-size: 12.5px; color: #64748B; margin: 0;">3 Link per platform (Instagram, TikTok, FB, Other Video). Manager dapat klik tombol Buka ↗ untuk langsung menuju URL postingan.</p>
                    </div>
                    @php
                        $platforms = [
                            'instagram' => ['label' => 'Instagram', 'icon_bg' => 'linear-gradient(135deg,#F58529,#DD2A7B,#8134AF)'],
                            'tiktok' => ['label' => 'TikTok', 'icon_bg' => '#000000'],
                            'facebook' => ['label' => 'Facebook', 'icon_bg' => '#1877F2'],
                            'snack_video' => ['label' => 'Other Video', 'icon_bg' => 'linear-gradient(135deg,#FF7E5F,#FEB47B)'],
                        ];
                    @endphp
                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        @foreach ($platforms as $pKey => $pMeta)
                            <div class="modal-link-box">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                                    <div style="width: 24px; height: 24px; border-radius: 6px; background: {{ $pMeta['icon_bg'] }}; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">
                                        {{ strtoupper(substr($pKey, 0, 2)) }}
                                    </div>
                                    <span style="font-weight: 800; font-size: 13.5px; color: #0F172A;">{{ $pMeta['label'] }}</span>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                                    @for ($slot = 1; $slot <= 3; $slot++)
                                        <div>
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                <label style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase;">
                                                    Slot #{{ $slot }}
                                                </label>
                                                <button type="button" id="openLink_{{ $pKey }}_{{ $slot }}" class="btn-open-link" style="display: none;" onclick="openExternalUrl('{{ $pKey }}', {{ $slot }})">
                                                    Buka Link ↗
                                                </button>
                                            </div>
                                            <input type="url" name="links[{{ $pKey }}][{{ $slot }}]" id="link_{{ $pKey }}_{{ $slot }}" class="modal-link-input" placeholder="https://..." oninput="updateLinkButton('{{ $pKey }}', {{ $slot }})">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Task 2 - 6: Upload PDF --}}
                @for ($t = 2; $t <= 6; $t++)
                    <div id="taskPane{{ $t }}" class="task-pane" style="display: none;">
                        <div style="margin-bottom: 20px;">
                            <h4 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 2px;">
                                {{ $tasksList[$t]['title'] }}
                            </h4>
                            <p style="font-size: 12.5px; color: #64748B; margin: 0;">
                                {{ $tasksList[$t]['desc'] }}
                            </p>
                        </div>

                        {{-- Box Berkas yang Sudah Diunggah Marketing --}}
                        <div id="pdfListBox{{ $t }}" class="current-file-container" style="margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 13px; font-weight: 800; color: #1E293B; text-transform: uppercase; letter-spacing: 0.03em;">
                                        Berkas Bukti Terkirim (PDF / Gambar)
                                    </span>
                                    <span style="font-size: 11px; padding: 2px 8px; border-radius: 999px; background: #E2E8F0; color: #475569; font-weight: 700;">Maks. 1 File</span>
                                </div>
                            </div>
                            <div id="pdfList{{ $t }}" style="display: flex; flex-direction: column; gap: 10px;">
                                <div class="empty-file-state">
                                    <div class="empty-file-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    </div>
                                    <span style="font-size: 13px; font-weight: 700; color: #475569;">Belum ada file bukti</span>
                                    <span style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">Marketing belum mengunggah file untuk tugas ini</span>
                                </div>
                            </div>
                        </div>

                        {{-- Area Upload Dropzone Modern oleh Manager --}}
                        <div>
                            <div class="modern-dropzone" id="dropzone_{{ $t }}" onclick="triggerFileInput({{ $t }})" ondragover="handleDragOver(event, {{ $t }})" ondragleave="handleDragLeave(event, {{ $t }})" ondrop="handleDrop(event, {{ $t }})">
                                <div class="dropzone-icon-circle">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                </div>
                                <p class="dropzone-primary-text">
                                    <strong>Klik untuk telusuri</strong> atau tarik & letakkan file bukti di sini
                                </p>
                                <p class="dropzone-sub-text">
                                    Format PDF / Gambar (Maks. 10MB) • Otomatis menggantikan file bukti sebelumnya
                                </p>
                                <button type="button" class="dropzone-browse-btn" tabindex="-1">Pilih File Bukti</button>
                                <input type="file" name="pdfs[{{ $t }}][]" id="fileInput_{{ $t }}" accept=".pdf,image/*,.jpg,.jpeg,.png,.webp" style="display: none;" onchange="handleModalFileSelected(this, {{ $t }})">
                            </div>

                            {{-- Preview File Yang Baru Dipilih (Sebelum Disimpan) --}}
                            <div id="selectedPreview_{{ $t }}" class="selected-file-preview-card" style="display: none;">
                                <div class="selected-file-left">
                                    <div class="selected-file-badge">PDF</div>
                                    <div style="min-width: 0;">
                                        <div class="selected-file-name" id="selectedFileName_{{ $t }}">-</div>
                                        <div class="selected-file-meta">
                                            <span id="selectedFileSize_{{ $t }}">0 KB</span> • <span style="font-weight: 700; color: #059669;">Siap diunggah (akan menggantikan file lama)</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn-cancel-selection" onclick="cancelModalFileSelected({{ $t }})">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                @endfor

                {{-- Task 7: Update Perkembangan Prospek (Per Tanggal Kirim CS) --}}
                <div id="taskPane7" class="task-pane" style="display: none;">
                    <div style="margin-bottom: 16px;">
                        <h4 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 2px;">
                            Update Perkembangan Prospek (Per Tanggal Pengiriman)
                        </h4>
                        <p style="font-size: 12.5px; color: #64748B; margin: 0;">
                            <strong>Aturan Tugas 7:</strong> Seluruh prospek yang dikirim oleh CS ke marketing pada tanggal ini wajib diisi catatannya agar kolom to-do pada tanggal pengiriman tersebut berstatus <strong>"X" (Hijau)</strong>.
                        </p>
                    </div>

                    {{-- Banner Status Kepatuhan Tanggal Ini --}}
                    <div id="modalTask7Banner" style="padding: 14px 16px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; border: 1.5px solid #E2E8F0; background: #F8FAFC;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span id="modalTask7BadgeIcon" style="font-size: 20px;">📋</span>
                            <div>
                                <div id="modalTask7StatusTitle" style="font-size: 13.5px; font-weight: 800; color: #0F172A;">Memuat status...</div>
                                <div id="modalTask7StatusDesc" style="font-size: 12px; margin-top: 1px; color: #64748B;">Menghitung kepatuhan prospek tanggal ini...</div>
                            </div>
                        </div>
                        <div id="modalTask7CountBadge" style="font-size: 16px; font-weight: 900; color: #475569;">-</div>
                    </div>

                    {{-- Container Daftar Prospek --}}
                    <div id="modalProspectsListBox">
                        <h5 style="font-size: 13px; font-weight: 700; color: #1E293B; margin: 0 0 10px; display: flex; align-items: center; gap: 6px;">
                            <span>Daftar Prospek Yang Dikirim CS Pada Tanggal Ini</span>
                        </h5>
                        <div id="modalProspectsList" style="display: flex; flex-direction: column; gap: 12px; max-height: 460px; overflow-y: auto; padding-right: 4px;">
                            <div style="text-align: center; padding: 20px; color: #94A3B8; font-style: italic;">Memuat prospek...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding: 18px 28px; border-top: 1px solid #E2E8F0; background: #F8FAFC; border-radius: 0 0 24px 24px; display: flex; justify-content: flex-end; align-items: center; gap: 12px;">
                <button type="button" onclick="closeTodoModal()" style="padding: 10px 20px; border-radius: 12px; border: 1px solid #CBD5E1; background: #FFFFFF; font-weight: 700; font-size: 13px; color: #475569; cursor: pointer; transition: all .15s ease;">
                    Tutup
                </button>
                <button type="submit" id="modalSubmitBtn" style="padding: 10px 24px; border-radius: 12px; border: 0; background: var(--primary-600); color: #fff; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25); transition: all .15s ease;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Form Hapus PDF Bukti --}}
<form id="deletePdfForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    let currentUserId = null;
    let currentDay = null;
    let currentTaskNum = 1;
    let modalToastTimer = null;

    const tasksMeta = {
        1: { title: 'Posting 12 Link Media Sosial', desc: 'Posting 12 link media sosial (Instagram, TikTok, Facebook, Other Video).' },
        2: { title: 'Broadcast & Komentar Sosial Media', desc: 'Bukti PDF broadcast & komentar media sosial.' },
        3: { title: 'Mengiklankan Akun Instagram', desc: 'Bukti PDF promosi/iklan akun Instagram.' },
        4: { title: 'DM Brosur', desc: 'Bukti PDF DM brosur (Pak Sabar, Pak Henry, Marketing).' },
        5: { title: 'Menyapa & Follow Up Klien Lama', desc: 'Bukti PDF sapa & follow up klien lama.' },
        6: { title: 'Memaparkan Rencana Penjualan', desc: 'Bukti PDF rencana dan skema penjualan.' },
        7: { title: 'Update Perkembangan Prospek', desc: 'Update catatan perkembangan prospek yang dikirim CS pada tanggal terkait. Kolom bernilai X jika seluruh prospek pada tanggal kirim tersebut telah diupdate.' }
    };

    function openTodoModal(userId, userName, dateStr, taskNum, day) {
        currentUserId = userId;
        currentTaskNum = parseInt(taskNum, 10);
        currentDay = day ? parseInt(day, 10) : parseInt(dateStr.split('-')[2], 10);

        const meta = tasksMeta[currentTaskNum] || { title: 'Tugas ' + currentTaskNum, desc: '' };

        document.getElementById('modalAvatar').innerText = userName.charAt(0).toUpperCase();
        document.getElementById('modalTitle').innerText = 'Tugas ' + currentTaskNum + ': ' + meta.title;
        document.getElementById('modalSubTitle').innerText = userName + ' • Tanggal ' + dateStr;
        document.getElementById('modalTaskBadge').innerText = '📌 ' + meta.desc;
        document.getElementById('modalInputDate').value = dateStr;
        document.getElementById('modalActiveTask').value = currentTaskNum;
        document.getElementById('modalUpdateForm').action = '/manager/todos/' + userId + '/update';

        // Reset notifikasi & progress
        document.getElementById('modalSuccessToast').style.display = 'none';
        document.getElementById('modalUploadProgressBox').style.display = 'none';
        const noteDateInfoEl = document.getElementById('modalNoteDateInfo');
        if (noteDateInfoEl) noteDateInfoEl.style.display = 'none';
        const submitBtn = document.getElementById('modalSubmitBtn');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Simpan Perubahan';
        }

        // Tampilkan hanya pane tugas yang diklik
        for (let i = 1; i <= 7; i++) {
            const pane = document.getElementById('taskPane' + i);
            if (pane) {
                if (i === currentTaskNum) {
                    pane.style.display = 'block';
                    pane.querySelectorAll('input, textarea, select, button').forEach(el => el.disabled = false);
                } else {
                    pane.style.display = 'none';
                    pane.querySelectorAll('input, textarea, select').forEach(el => el.disabled = true);
                }
            }
        }

        document.getElementById('todoEditModal').classList.add('active');

        // Reset form inputs & link buttons
        document.querySelectorAll('.modal-link-input').forEach(i => i.value = '');
        document.querySelectorAll('[id^="openLink_"]').forEach(btn => btn.style.display = 'none');
        const modalNoteEl = document.getElementById('modalNote');
        if (modalNoteEl) modalNoteEl.value = '';
        for (let t = 2; t <= 6; t++) {
            cancelModalFileSelected(t);
            const listEl = document.getElementById('pdfList' + t);
            if (listEl) {
                listEl.innerHTML = '<span style="font-size: 13px; color: #94A3B8; font-style: italic;">Memuat berkas...</span>';
            }
        }

        document.getElementById('modalLoading').style.display = 'block';

        // Fetch data
        fetch('/manager/todos/' + userId + '/data?date=' + dateStr)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modalLoading').style.display = 'none';
                if (data.formattedDate) {
                    document.getElementById('modalSubTitle').innerText = userName + ' • ' + data.formattedDate;
                }

                // Populate Tugas 1
                if (currentTaskNum === 1 && data.links) {
                    for (const [pKey, slots] of Object.entries(data.links)) {
                        for (const [slot, linkObj] of Object.entries(slots)) {
                            const inp = document.getElementById('link_' + pKey + '_' + slot);
                            if (inp && linkObj.url) {
                                inp.value = linkObj.url;
                                updateLinkButton(pKey, slot);
                            }
                        }
                    }
                }

                // Populate Tugas 7
                if (currentTaskNum === 7) {
                    renderManagerTask7Prospects(data);
                }

                // Populate Tugas 2-6 (PDFs)
                if (currentTaskNum >= 2 && currentTaskNum <= 6 && data.pdfs) {
                    const pdfs = data.pdfs[currentTaskNum] || [];
                    renderPdfList(currentTaskNum, pdfs, false);
                }
            })
            .catch(err => {
                document.getElementById('modalLoading').innerText = 'Gagal memuat data to-do.';
            });
    }

    function triggerFileInput(taskNum) {
        const input = document.getElementById('fileInput_' + taskNum);
        if (input) input.click();
    }

    function handleDragOver(e, taskNum) {
        e.preventDefault();
        e.stopPropagation();
        const dropzone = document.getElementById('dropzone_' + taskNum);
        if (dropzone) dropzone.classList.add('dragover');
    }

    function handleDragLeave(e, taskNum) {
        e.preventDefault();
        e.stopPropagation();
        const dropzone = document.getElementById('dropzone_' + taskNum);
        if (dropzone) dropzone.classList.remove('dragover');
    }

    function isAllowedBuktiFile(file) {
        if (!file) return false;
        const name = (file.name || '').toLowerCase();
        const validExt = ['.pdf', '.jpg', '.jpeg', '.png', '.webp'].some(ext => name.endsWith(ext));
        const validMime = file.type === 'application/pdf' || file.type.startsWith('image/');
        return validExt || validMime;
    }

    function handleDrop(e, taskNum) {
        e.preventDefault();
        e.stopPropagation();
        const dropzone = document.getElementById('dropzone_' + taskNum);
        if (dropzone) dropzone.classList.remove('dragover');

        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
            const file = dt.files[0];
            if (!isAllowedBuktiFile(file)) {
                alert('Hanya file dokumen PDF atau Gambar (JPG, PNG, WEBP) yang diperbolehkan!');
                return;
            }
            const input = document.getElementById('fileInput_' + taskNum);
            if (input) {
                const newDt = new DataTransfer();
                newDt.items.add(file);
                input.files = newDt.files;
                handleModalFileSelected(input, taskNum);
            }
        }
    }

    function formatFileSize(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function handleModalFileSelected(input, taskNum) {
        const preview = document.getElementById('selectedPreview_' + taskNum);
        const nameEl = document.getElementById('selectedFileName_' + taskNum);
        const sizeEl = document.getElementById('selectedFileSize_' + taskNum);
        const dropzone = document.getElementById('dropzone_' + taskNum);

        if (input && input.files && input.files.length > 0) {
            const file = input.files[0];
            if (!isAllowedBuktiFile(file)) {
                alert('Hanya file bukti format PDF atau Gambar (JPG, PNG, WEBP) yang diperbolehkan!');
                input.value = '';
                if (preview) preview.style.display = 'none';
                if (dropzone) dropzone.classList.remove('has-file');
                return;
            }

            const isImg = file.type.startsWith('image/') || /\.(jpg|jpeg|png|webp)$/i.test(file.name);
            const badgeEl = preview ? preview.querySelector('.selected-file-badge') : null;
            if (badgeEl) {
                const ext = file.name.split('.').pop().toUpperCase();
                badgeEl.textContent = isImg ? (ext === 'JPEG' ? 'JPG' : ext) : 'PDF';
                badgeEl.classList.toggle('is-img', isImg);
            }

            if (nameEl) nameEl.innerText = file.name;
            if (sizeEl) sizeEl.innerText = formatFileSize(file.size);
            if (preview) preview.style.display = 'flex';
            if (dropzone) dropzone.classList.add('has-file');
        } else {
            if (preview) preview.style.display = 'none';
            if (dropzone) dropzone.classList.remove('has-file');
        }
    }

    function cancelModalFileSelected(taskNum) {
        const input = document.getElementById('fileInput_' + taskNum);
        const preview = document.getElementById('selectedPreview_' + taskNum);
        const dropzone = document.getElementById('dropzone_' + taskNum);
        if (input) input.value = '';
        if (preview) preview.style.display = 'none';
        if (dropzone) dropzone.classList.remove('has-file');
    }

    function renderManagerTask7Prospects(data) {
        const banner = document.getElementById('modalTask7Banner');
        const icon = document.getElementById('modalTask7BadgeIcon');
        const title = document.getElementById('modalTask7StatusTitle');
        const desc = document.getElementById('modalTask7StatusDesc');
        const countBadge = document.getElementById('modalTask7CountBadge');
        const listContainer = document.getElementById('modalProspectsList');

        const targetCount = data.target_count || 0;
        const completedCount = data.completed_prospects_count || 0;
        const isComplete = !!data.is_week_complete;
        const weekRange = data.week_range || '';

        if (targetCount > 0) {
            countBadge.innerText = completedCount + ' / ' + targetCount + ' Terupdate';
            if (isComplete) {
                banner.style.background = '#F0FDF4';
                banner.style.borderColor = '#A7F3D0';
                icon.innerText = '🎉';
                title.innerText = '✓ Kepatuhan Tugas 7 Terpenuhi (' + weekRange + ')';
                title.style.color = '#065F46';
                desc.innerText = 'Seluruh ' + targetCount + ' prospek yang dikirim pada tanggal ini telah diupdate. To Do 7 tanggal ini berstatus "X" (Hijau).';
                desc.style.color = '#047857';
                countBadge.style.color = '#059669';
            } else {
                banner.style.background = '#FFFBEB';
                banner.style.borderColor = '#FDE68A';
                icon.innerText = '⚠️';
                title.innerText = 'Kepatuhan Belum Terpenuhi (' + weekRange + ')';
                title.style.color = '#92400E';
                desc.innerText = 'Baru terupdate ' + completedCount + ' dari ' + targetCount + ' prospek yang dikirim tanggal ini. Sisa ' + (targetCount - completedCount) + ' prospek lagi agar To Do 7 tanggal ini berstatus "X".';
                desc.style.color = '#B45309';
                countBadge.style.color = '#D97706';
            }
        } else {
            banner.style.background = '#F8FAFC';
            banner.style.borderColor = '#E2E8F0';
            icon.innerText = 'ℹ️';
            title.innerText = 'Tidak Ada Prospek Masuk (' + weekRange + ')';
            title.style.color = '#1E293B';
            desc.innerText = 'Tidak ada prospek yang dikirim oleh CS ke marketing ini pada tanggal tersebut.';
            desc.style.color = '#64748B';
            countBadge.innerText = '0 Prospek';
            countBadge.style.color = '#64748B';
        }

        if (!listContainer) return;

        if (!data.prospects_list || data.prospects_list.length === 0) {
            listContainer.innerHTML = `
                <div style="text-align: center; padding: 22px 16px; background: #F8FAFC; border: 1.5px dashed #CBD5E1; border-radius: 12px; color: #64748B;">
                    <div style="font-size: 13px; font-weight: 700; color: #334155;">Tidak ada prospek yang dikirim pada tanggal ini</div>
                    <div style="font-size: 11.5px; margin-top: 2px;">CS tidak mengirimkan prospek ke marketing ini pada tanggal tersebut.</div>
                </div>
            `;
            return;
        }

        let html = '';
        data.prospects_list.forEach((p, idx) => {
            const isUp = !!p.has_update;
            const waPhone = (p.phone || '').replace(/[^0-9]/g, '');
            const waLink = waPhone.startsWith('0') ? '62' + waPhone.substring(1) : waPhone;

            html += `
                <div style="border: 1.5px solid ${isUp ? '#A7F3D0' : '#E2E8F0'}; background: ${isUp ? '#F0FDF4' : '#FFFFFF'}; border-radius: 12px; padding: 12px 14px; transition: all .2s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="width: 24px; height: 24px; border-radius: 6px; background: ${isUp ? '#10B981' : '#64748B'}; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">
                                ${idx + 1}
                            </span>
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span style="font-size: 13.5px; font-weight: 800; color: #0F172A; font-family: monospace;">
                                        ${p.phone || '-'}
                                    </span>
                                    ${waLink ? `<a href="https://wa.me/${waLink}" target="_blank" rel="noopener" style="padding: 1px 6px; border-radius: 4px; background: #25D366; color: #fff; font-size: 10.5px; font-weight: 700; text-decoration: none;">WA</a>` : ''}
                                    <span style="padding: 1px 7px; border-radius: 4px; background: #EFF6FF; color: #1D4ED8; font-size: 11px; font-weight: 700;">
                                        ${p.service || '-'}
                                    </span>
                                </div>
                                <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                                    Masuk: <strong>${p.entry_date || '-'}</strong> • Pengirim: <strong>${p.sender || '-'}</strong>
                                </div>
                            </div>
                        </div>

                        <div>
                            ${isUp
                                ? `<span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 999px; background: #D1FAE5; color: #065F46; font-size: 11px; font-weight: 700; border: 1px solid #A7F3D0;">
                                    <span style="width: 5px; height: 5px; border-radius: 50%; background: #10B981;"></span>
                                    Sudah Diupdate ${p.updated_at ? `<span style="font-weight: 500; opacity: 0.8;">(${p.updated_at})</span>` : ''}
                                   </span>`
                                : `<span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 999px; background: #FEF3C7; color: #92400E; font-size: 11px; font-weight: 700; border: 1px solid #FDE68A;">
                                    <span style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></span>
                                    Wajib Diisi Minggu Ini
                                   </span>`
                            }
                        </div>
                    </div>

                    <div>
                        <textarea
                            name="prospect_updates[${p.id}][note]"
                            rows="2"
                            placeholder="Catatan perkembangan prospek ini (dapat diedit oleh Manager)..."
                            style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid ${isUp ? '#A7F3D0' : '#CBD5E1'}; font-family: inherit; font-size: 12.5px; line-height: 1.4; color: #0F172A; background: #fff; resize: vertical;"
                        >${p.note || ''}</textarea>
                    </div>
                </div>
            `;
        });

        listContainer.innerHTML = html;
    }

    function renderPdfList(taskNum, pdfs, isHighlightFirst = false) {
        const container = document.getElementById('pdfList' + taskNum);
        if (!container) return;

        if (!pdfs || pdfs.length === 0) {
            container.innerHTML = `
                <div class="empty-file-state">
                    <div class="empty-file-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <span style="font-size: 13px; font-weight: 700; color: #475569;">Belum ada file bukti</span>
                    <span style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">Marketing belum mengunggah file untuk tugas ini</span>
                </div>
            `;
            return;
        }

        let html = '';
        pdfs.forEach((pdf, idx) => {
            const isNew = (isHighlightFirst && idx === 0);
            const fileName = pdf.original_name || pdf.file_path || '';
            const extMatch = fileName.match(/\.([a-z0-9]+)$/i);
            const ext = extMatch ? extMatch[1].toLowerCase() : '';
            const isImg = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);
            const badgeText = isImg ? (ext === 'jpeg' ? 'JPG' : ext.toUpperCase()) : 'PDF';
            const iconSvg = isImg
                ? `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>`
                : `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>`;

            html += `
                <div class="modal-file-card ${isNew ? 'modal-file-card-new' : ''}" id="pdfCard_${pdf.id}">
                    <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                        <div class="pdf-icon-badge ${isImg ? 'is-img' : ''}">
                            ${iconSvg}
                            <span>${badgeText}</span>
                        </div>
                        <div style="min-width: 0;">
                            <div class="pdf-filename" title="${fileName}">
                                ${fileName}
                                ${isNew ? '<span class="badge-new-upload">Baru Diunggah</span>' : ''}
                            </div>
                            <div class="pdf-filemeta">
                                <span class="badge-verified-dot"></span>
                                <span>Tersimpan di server</span>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                        <a href="/storage/${pdf.file_path}" target="_blank" class="btn-file-preview">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            Lihat File
                        </a>
                        <button type="button" onclick="deletePdfModal(${pdf.id}, ${taskNum})" class="btn-file-delete" title="Hapus File Ini">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            Hapus
                        </button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function showSuccessNotification(message) {
        const toast = document.getElementById('modalSuccessToast');
        const msgEl = document.getElementById('modalSuccessToastMsg');
        if (toast && msgEl) {
            msgEl.innerText = message;
            toast.style.display = 'flex';

            clearTimeout(modalToastTimer);
            modalToastTimer = setTimeout(() => {
                toast.style.display = 'none';
            }, 4500);
        }
        // Catatan: Tidak memanggil AppSwal.toast di sini agar tidak memicu layar hitam / backdrop di samping modal
    }

    // Tangani Submit Form Modal via AJAX untuk progress upload & instant file update
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('modalUpdateForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const taskNum = parseInt(document.getElementById('modalActiveTask').value, 10);
            const btnSubmit = document.getElementById('modalSubmitBtn');
            const progressBox = document.getElementById('modalUploadProgressBox');
            const progressBar = document.getElementById('modalUploadProgressBar');
            const progressPercent = document.getElementById('modalUploadProgressPercent');
            const progressText = document.getElementById('modalUploadProgressText');
            const successToast = document.getElementById('modalSuccessToast');

            // Cek apakah ada upload file pada tugas 2-6
            const fileInput = document.querySelector(`input[name="pdfs[${taskNum}][]"]`);
            const hasFiles = fileInput && fileInput.files && fileInput.files.length > 0;

            // Update UI State: Button spinner & Progress bar
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="upload-btn-spinner"></span> ' + (hasFiles ? 'Mengunggah Berkas...' : 'Menyimpan...');

            if (hasFiles) {
                progressBox.style.display = 'block';
                progressBar.style.width = '0%';
                progressPercent.innerText = '0%';
                progressText.innerText = 'Mengunggah berkas PDF baru (menggantikan file lama)...';
            } else {
                progressBox.style.display = 'none';
            }
            successToast.style.display = 'none';

            const formData = new FormData(form);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');

            if (hasFiles) {
                xhr.upload.onprogress = function(evt) {
                    if (evt.lengthComputable) {
                        const percent = Math.round((evt.loaded / evt.total) * 100);
                        progressBar.style.width = percent + '%';
                        progressPercent.innerText = percent + '%';
                        if (percent >= 100) {
                            progressText.innerText = 'Menyimpan berkas di server...';
                        }
                    }
                };
            }

            xhr.onload = function() {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Simpan Perubahan';
                progressBox.style.display = 'none';

                if (xhr.status >= 200 && xhr.status < 300) {
                    let res;
                    try {
                        res = JSON.parse(xhr.responseText);
                    } catch (err) {
                        res = { success: true, message: 'Perubahan to-do berhasil disimpan.' };
                    }

                    // Tugas 2-6: Render ulang daftar file, berkas baru langsung tampil di posisi paling atas
                    if (taskNum >= 2 && taskNum <= 6 && res.pdfs) {
                        const pdfs = res.pdfs[taskNum] || [];
                        renderPdfList(taskNum, pdfs, hasFiles);

                        // Reset input dan preview pilihan berkas
                        cancelModalFileSelected(taskNum);

                        // Scroll container berkas ke atas agar file baru langsung terlihat
                        const containerBox = document.getElementById('pdfListBox' + taskNum);
                        if (containerBox) {
                            containerBox.scrollTop = 0;
                            containerBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }

                    // Update indikator cell pada tabel matrix to-do secara real-time
                    if (currentUserId && res.task) {
                        const daysToUpdate = (res.task === 7 && res.affectedDays && res.affectedDays.length > 0)
                            ? res.affectedDays
                            : (res.day ? [res.day] : []);

                        daysToUpdate.forEach(d => {
                            const cell = document.getElementById('matrix_cell_' + currentUserId + '_' + res.task + '_' + d);
                            if (cell) {
                                if (res.isCompleted) {
                                    cell.className = 'cell-state filled cell-clickable';
                                    cell.innerText = 'X';
                                } else {
                                    cell.className = 'cell-state empty cell-clickable';
                                    cell.innerText = '';
                                }
                            }
                        });
                    }

                    // Tampilkan notifikasi berhasil elegan
                    showSuccessNotification(res.message || 'Perubahan to-do berhasil disimpan!');
                } else {
                    let errorMsg = 'Gagal menyimpan to-do.';
                    try {
                        const errData = JSON.parse(xhr.responseText);
                        if (errData.message) errorMsg = errData.message;
                    } catch (e) {}

                    if (window.AppSwal && AppSwal.error) {
                        AppSwal.error('Gagal Menyimpan', errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                }
            };

            xhr.onerror = function() {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Simpan Perubahan';
                progressBox.style.display = 'none';
                if (window.AppSwal && AppSwal.error) {
                    AppSwal.error('Koneksi Gagal', 'Terjadi kesalahan jaringan saat mengunggah file.');
                } else {
                    alert('Terjadi kesalahan jaringan saat mengunggah file.');
                }
            };

            xhr.send(formData);
        });
    });

    function deletePdfModal(pdfId, taskNum) {
        const doDelete = () => {
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            fetch('/manager/todos/pdfs/' + pdfId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Animasi hapus kartu berkas dengan halus
                    const card = document.getElementById('pdfCard_' + pdfId);
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            card.remove();
                            const container = document.getElementById('pdfList' + taskNum);
                            if (container && container.querySelectorAll('.modal-file-card').length === 0) {
                                container.innerHTML = `
                                    <div class="empty-file-state">
                                        <div class="empty-file-icon">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        </div>
                                        <span style="font-size: 13px; font-weight: 700; color: #475569;">Belum ada file bukti PDF</span>
                                        <span style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">Marketing belum mengunggah file untuk tugas ini</span>
                                    </div>
                                `;
                            }
                        }, 300);
                    }

                    // Perbarui status cell matrix jika berkas habis
                    if (data.userId && data.day && data.task) {
                        const cell = document.getElementById('matrix_cell_' + data.userId + '_' + data.task + '_' + data.day);
                        if (cell) {
                            if (data.isCompleted) {
                                cell.className = 'cell-state filled cell-clickable';
                                cell.innerText = 'X';
                            } else {
                                cell.className = 'cell-state empty cell-clickable';
                                cell.innerText = '';
                            }
                        }
                    }

                    showSuccessNotification(data.message || 'File PDF berhasil dihapus.');
                } else {
                    alert(data.message || 'Gagal menghapus file.');
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan saat menghapus file.');
            });
        };

        if (window.AppSwal && AppSwal.confirm) {
            AppSwal.confirm('Hapus File Bukti?', 'File PDF ini akan dihapus permanen dari server.', true, 'Ya, Hapus').then(res => {
                if (res.isConfirmed) doDelete();
            });
        } else {
            if (confirm('Yakin ingin menghapus file PDF bukti ini?')) {
                doDelete();
            }
        }
    }

    function getValidUrl(url) {
        if (!url) return '';
        url = url.trim();
        if (!url) return '';
        if (!/^https?:\/\//i.test(url)) {
            return 'https://' + url;
        }
        return url;
    }

    function openExternalUrl(pKey, slot) {
        const inp = document.getElementById('link_' + pKey + '_' + slot);
        if (!inp) return;
        const validUrl = getValidUrl(inp.value);
        if (validUrl) {
            window.open(validUrl, '_blank', 'noopener,noreferrer');
        }
    }

    function updateLinkButton(pKey, slot) {
        const inp = document.getElementById('link_' + pKey + '_' + slot);
        const btn = document.getElementById('openLink_' + pKey + '_' + slot);
        if (inp && btn) {
            const val = inp.value.trim();
            if (val !== '') {
                btn.style.display = 'inline-flex';
            } else {
                btn.style.display = 'none';
            }
        }
    }

    function closeTodoModal() {
        document.getElementById('todoEditModal').classList.remove('active');
    }

    // --- EXPORT ASYNCHRONOUS BACKGROUND JOB SYSTEM ---
    let exportPollTimer = null;
    let activeExportId = null;
    const currentFilterMonth = {{ (int) $month }};
    const currentFilterYear = {{ (int) $year }};

    function startAsyncExport() {
        const btn = document.getElementById('btnStartExportZip');
        const btnText = document.getElementById('btnExportZipText');

        if (btn) btn.disabled = true;
        if (btnText) btnText.innerText = 'Menyiapkan Export...';

        renderLiveBannerProcessing(0, 0, 0, 'Memulai antrean background job...');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || '{{ csrf_token() }}';

        fetch('{{ route("manager.todos.export.start", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                month: currentFilterMonth,
                year: currentFilterYear
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.export_id) {
                activeExportId = data.export_id;
                try { sessionStorage.setItem('has_active_export', '1'); } catch (e) {}
                renderLiveBannerProcessing(data.percent || 0, data.processed || 0, data.total || 0, data.message);
                pollExportStatus(data.export_id);
                if (window.refreshExportNotifications) window.refreshExportNotifications(true);
            } else {
                try { sessionStorage.removeItem('has_active_export'); } catch (e) {}
                renderLiveBannerError(data.message || 'Gagal memulai proses export.');
                resetExportButton();
            }
        })
        .catch(err => {
            try { sessionStorage.removeItem('has_active_export'); } catch (e) {}
            renderLiveBannerError('Terjadi kesalahan jaringan saat memulai export.');
            resetExportButton();
        });
    }

    function pollExportStatus(exportId) {
        if (exportPollTimer) clearTimeout(exportPollTimer);

        fetch('/manager/todos/export/status/' + exportId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'completed') {
                try { sessionStorage.removeItem('has_active_export'); } catch (e) {}
                renderLiveBannerCompleted(data);
                resetExportButton();
                if (window.refreshExportNotifications) window.refreshExportNotifications(false);
                if (window.AppSwal && AppSwal.toast) {
                    AppSwal.toast('success', 'Export Excel To-Do (.ZIP) selesai!');
                }
            } else if (data.status === 'failed') {
                try { sessionStorage.removeItem('has_active_export'); } catch (e) {}
                renderLiveBannerError(data.error_message || 'Proses pembuatan export gagal di server.');
                resetExportButton();
                if (window.refreshExportNotifications) window.refreshExportNotifications(false);
            } else {
                renderLiveBannerProcessing(data.percent, data.processed, data.total);
                exportPollTimer = setTimeout(() => pollExportStatus(exportId), 1500);
            }
        })
        .catch(err => {
            exportPollTimer = setTimeout(() => pollExportStatus(exportId), 3000);
        });
    }

    function renderLiveBannerProcessing(percent, processed, total, customMsg) {
        const banner = document.getElementById('exportLiveBanner');
        if (!banner) return;

        banner.style.display = 'block';
        banner.style.background = 'linear-gradient(135deg, #EFF6FF 0%, #FFFFFF 100%)';
        banner.style.border = '1.5px solid #BFDBFE';

        const iconBox = document.getElementById('exportLiveIconBox');
        iconBox.style.background = '#3B82F6';
        iconBox.style.color = '#FFFFFF';
        iconBox.innerHTML = '<div style="display:inline-block; width:20px; height:20px; border:2.5px solid rgba(255,255,255,0.3); border-top-color:#fff; border-radius:50%; animation:spin 0.8s linear infinite;"></div>';

        document.getElementById('exportLiveTitle').innerText = 'Memproses Export To-Do Marketing di Background...';
        document.getElementById('exportLiveSubtitle').innerText = customMsg || `Menghasilkan file Excel matriks per marketing (${processed} dari ${total} selesai)...`;

        document.getElementById('exportLiveProgressWrap').style.display = 'block';
        document.getElementById('exportLiveCountText').innerText = `${processed} dari ${total} Marketing Selesai`;
        document.getElementById('exportLivePercentText').innerText = `${percent}%`;
        document.getElementById('exportLiveProgressBar').style.width = `${percent}%`;

        document.getElementById('exportLiveActionBox').innerHTML = `
            <span style="font-size:12px; font-weight:700; color:#2563EB; background:#DBEAFE; padding:4px 10px; border-radius:999px;">
                Memproses ${percent}%
            </span>
        `;
    }

    function renderLiveBannerCompleted(data) {
        const banner = document.getElementById('exportLiveBanner');
        if (!banner) return;

        banner.style.display = 'block';
        banner.style.background = 'linear-gradient(135deg, #F0FDF4 0%, #FFFFFF 100%)';
        banner.style.border = '1.5px solid #A7F3D0';

        const iconBox = document.getElementById('exportLiveIconBox');
        iconBox.style.background = '#10B981';
        iconBox.style.color = '#FFFFFF';
        iconBox.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>';

        document.getElementById('exportLiveTitle').innerText = 'Export To-Do Berhasil Disiapkan!';
        document.getElementById('exportLiveSubtitle').innerText = `File arsip ${data.filename || 'ZIP'} siap diunduh (${data.total || 0} marketing).`;

        document.getElementById('exportLiveProgressWrap').style.display = 'none';

        document.getElementById('exportLiveActionBox').innerHTML = `
            <a href="${data.download_url}" class="btn btn-primary" style="padding:8px 18px; font-size:13px; font-weight:700; background:#059669; border:none; display:inline-flex; align-items:center; gap:8px; border-radius:10px; color:#fff; text-decoration:none; box-shadow:0 4px 12px rgba(5,150,105,0.25);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Unduh File (.ZIP)</span>
            </a>
            <button type="button" onclick="dismissExportBanner()" style="background:transparent; border:none; color:#64748B; cursor:pointer; font-size:18px; padding:4px 8px;" title="Tutup">
                ✕
            </button>
        `;
    }

    function renderLiveBannerError(errorMessage) {
        const banner = document.getElementById('exportLiveBanner');
        if (!banner) return;

        banner.style.display = 'block';
        banner.style.background = 'linear-gradient(135deg, #FEF2F2 0%, #FFFFFF 100%)';
        banner.style.border = '1.5px solid #FECACA';

        const iconBox = document.getElementById('exportLiveIconBox');
        iconBox.style.background = '#EF4444';
        iconBox.style.color = '#FFFFFF';
        iconBox.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';

        document.getElementById('exportLiveTitle').innerText = 'Gagal Menyiapkan File Export';
        document.getElementById('exportLiveSubtitle').innerText = errorMessage || 'Terjadi kesalahan pada sistem antrean server.';

        document.getElementById('exportLiveProgressWrap').style.display = 'none';

        document.getElementById('exportLiveActionBox').innerHTML = `
            <button type="button" onclick="startAsyncExport()" class="btn btn-sm" style="padding:6px 14px; font-size:12px; font-weight:700; background:#DC2626; color:#fff; border:none; border-radius:8px; cursor:pointer;">
                Coba Lagi
            </button>
            <button type="button" onclick="dismissExportBanner()" style="background:transparent; border:none; color:#64748B; cursor:pointer; font-size:18px; padding:4px 8px;" title="Tutup">
                ✕
            </button>
        `;
    }

    function dismissExportBanner() {
        const banner = document.getElementById('exportLiveBanner');
        if (banner) banner.style.display = 'none';
        if (exportPollTimer) clearTimeout(exportPollTimer);
    }

    function resetExportButton() {
        const btn = document.getElementById('btnStartExportZip');
        const btnText = document.getElementById('btnExportZipText');
        if (btn) btn.disabled = false;
        if (btnText) btnText.innerText = 'Export Semua (.ZIP)';
    }

    // Periksa status export aktif saat halaman pertama kali dimuat (hanya jika ada export yang sedang berjalan)
    document.addEventListener('DOMContentLoaded', function() {
        if (sessionStorage.getItem('has_active_export') !== '1') return;

        fetch('{{ route("manager.todos.export.recent", [], false) }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.exports && data.exports.length > 0) {
                const activeExport = data.exports.find(x => x.status === 'processing' || x.status === 'pending');
                if (activeExport) {
                    activeExportId = activeExport.id;
                    renderLiveBannerProcessing(activeExport.percent, activeExport.processed, activeExport.total);
                    const btn = document.getElementById('btnStartExportZip');
                    const btnText = document.getElementById('btnExportZipText');
                    if (btn) btn.disabled = true;
                    if (btnText) btnText.innerText = 'Menyiapkan...';
                    pollExportStatus(activeExport.id);
                } else {
                    try { sessionStorage.removeItem('has_active_export'); } catch (e) {}
                }
            } else {
                try { sessionStorage.removeItem('has_active_export'); } catch (e) {}
            }
        })
        .catch(() => {});
    });
</script>
@endsection
