<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HIVEFIVE Prospect System')</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/hiv.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/hiv.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.AppToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            scrollbarPadding: false,
            customClass: {
                popup: 'swal2-modern-toast',
                title: 'swal2-modern-toast-title',
            },
            didOpen: function(toast) {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        window.AppSwal = {
            fire: function(opts) {
                opts = opts || {};
                var isDanger = opts.icon === 'warning' || opts.isDanger;
                var confirmBtnClass = 'swal2-modern-btn ' + (isDanger ? 'btn-confirm-danger' : (opts.icon === 'success' ? 'btn-confirm-success' : 'btn-confirm-primary'));
                return Swal.fire(Object.assign({
                    customClass: {
                        popup: 'swal2-modern-modal',
                        title: 'swal2-modern-title',
                        htmlContainer: 'swal2-modern-html',
                        confirmButton: confirmBtnClass,
                        cancelButton: 'swal2-modern-btn btn-cancel-neutral',
                        actions: 'swal2-modern-actions',
                    },
                    buttonsStyling: false,
                }, opts));
            },
            confirm: function(title, text, isDanger, confirmText) {
                if (typeof isDanger === 'undefined') isDanger = false;
                if (typeof confirmText === 'undefined') confirmText = isDanger ? 'Ya, Hapus' : 'Ya, Lanjutkan';
                return this.fire({
                    title: title || 'Konfirmasi',
                    text: text || 'Apakah Anda yakin?',
                    icon: isDanger ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    isDanger: isDanger,
                });
            },
            success: function(title, text) {
                return this.fire({
                    title: title || 'Berhasil',
                    text: text || '',
                    icon: 'success',
                    confirmButtonText: 'Tutup',
                });
            },
            error: function(title, text) {
                return this.fire({
                    title: title || 'Terjadi Kesalahan',
                    text: text || '',
                    icon: 'error',
                    confirmButtonText: 'Tutup',
                    isDanger: true,
                });
            },
            toast: function(icon, title) {
                icon = icon || 'success';
                return AppToast.fire({
                    icon: icon,
                    title: title,
                    customClass: {
                        popup: 'swal2-modern-toast swal2-toast-' + icon,
                        title: 'swal2-modern-toast-title',
                    }
                });
            }
        };
    </script>

    <style>
        :root {
            --primary-50: #EFF6FF;
            --primary-100: #DBEAFE;
            --primary-200: #BFDBFE;
            --primary-500: #2563EB;
            --primary-600: #1D4ED8;
            --primary-700: #1E40AF;
            --accent-green: #10B981;
            --accent-orange: #F59E0B;
            --background: #F8FAFC;
            --surface: #FFFFFF;
            --border: #E2E8F0;
            --text-primary: #172554;
            --text-secondary: #64748B;
            --text-muted: #94A3B8;
            --shadow-card: 0 4px 16px rgba(15, 23, 42, 0.04);
            --shadow-hover: 0 8px 24px rgba(15, 23, 42, 0.07);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--background);
            color: var(--text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        a {
            color: var(--primary-500);
            text-decoration: none;
        }

        a:hover {
            color: var(--primary-600);
        }

        /* ============== HEADER ============== */
        .app-header {
            height: 76px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .app-header-inner {
            max-width: 1320px;
            height: 100%;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .app-header .brand img {
            height: 42px;
            width: auto;
            display: block;
            flex-shrink: 0;
        }

        /* Primary nav (icon + label, compact) */
        .app-nav {
            display: flex;
            align-items: center;
            gap: 2px;
            flex: 1;
            justify-content: center;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-secondary);
            border-radius: 8px;
            transition: color .15s, background .15s;
            position: relative;
        }

        .nav-link svg {
            opacity: .85;
        }

        .nav-link:hover {
            color: var(--primary-600);
            background: var(--primary-50);
            text-decoration: none;
        }

        .nav-link:hover svg {
            opacity: 1;
        }

        .nav-link.active {
            color: var(--primary-600);
            background: var(--primary-50);
        }

        .nav-link.active svg {
            opacity: 1;
            color: var(--primary-600);
        }

        .nav-link.active::after {
            content: "";
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: -22px;
            height: 2px;
            background: var(--primary-500);
            border-radius: 2px 2px 0 0;
        }

        /* Nav divider */
        .nav-divider {
            width: 1px;
            height: 18px;
            background: var(--border);
            margin: 0 4px;
        }

        /* Dropdown nav (⋯ More) */
        .nav-dd {
            position: relative;
        }

        .nav-dd-toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-secondary);
            background: transparent;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s, border-color .15s;
        }

        .nav-dd-toggle:hover {
            background: var(--primary-50);
            color: var(--primary-600);
        }

        .nav-dd-toggle.active-cat {
            background: var(--primary-50);
            color: var(--primary-600);
            border-color: var(--primary-200);
        }

        .nav-dd-panel {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .12);
            padding: 6px;
            display: none;
            z-index: 60;
        }

        .nav-dd-panel.open {
            display: block;
        }

        .nav-dd-panel::before {
            content: "";
            position: absolute;
            top: -6px;
            right: 18px;
            width: 12px;
            height: 12px;
            background: var(--surface);
            border-left: 1px solid var(--border);
            border-top: 1px solid var(--border);
            transform: rotate(45deg);
        }

        .nav-dd-section {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--text-muted);
            padding: 8px 10px 4px;
            border-top: 1px solid var(--border);
            margin-top: 4px;
        }

        .nav-dd-section:first-child {
            border-top: 0;
            margin-top: 0;
            padding-top: 4px;
        }

        .nav-dd-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            transition: background .15s ease;
        }

        .nav-dd-item:hover {
            background: var(--primary-50);
            color: var(--primary-700);
            text-decoration: none;
        }

        .nav-dd-item svg {
            color: var(--text-secondary);
            flex-shrink: 0;
        }

        .nav-dd-item:hover svg {
            color: var(--primary-600);
        }

        .nav-dd-item.active {
            background: var(--primary-500);
            color: #fff;
        }

        .nav-dd-item.active svg {
            color: #fff;
        }

        .app-user {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .role-pill {
            background: var(--primary-50);
            color: var(--primary-600);
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
            line-height: 1.4;
        }

        .user-name {
            display: none;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-500), #6366F1);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            flex-shrink: 0;
            cursor: pointer;
            border: none;
            padding: 0;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 0 0 3px var(--primary-100);
        }

        .user-dd-wrap {
            position: relative;
        }

        .user-dd-panel {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            min-width: 240px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .12), 0 2px 6px rgba(15, 23, 42, .06);
            padding: 6px;
            z-index: 100;
            display: none;
        }

        .user-dd-panel.open {
            display: block;
        }

        .user-dd-panel::before {
            content: "";
            position: absolute;
            top: -6px;
            right: 14px;
            width: 12px;
            height: 12px;
            background: var(--surface);
            border-left: 1px solid var(--border);
            border-top: 1px solid var(--border);
            transform: rotate(45deg);
        }

        .user-dd-head {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 4px;
        }

        .user-dd-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.25;
            word-break: break-word;
        }

        .user-dd-meta {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
            line-height: 1.4;
            word-break: break-word;
        }

        .user-dd-meta .role-chip {
            display: inline-block;
            background: var(--primary-50);
            color: var(--primary-600);
            padding: 2px 7px;
            border-radius: 999px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-size: 11px;
            margin-right: 4px;
        }

        .user-dd-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
            cursor: pointer;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }

        .user-dd-item:hover {
            background: #FEF2F2;
            color: #B91C1C;
        }

        .user-dd-item svg {
            flex-shrink: 0;
        }

        /* Export Background Notification Bell */
        .notif-dd-wrap {
            position: relative;
        }

        .notif-bell-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
        }

        .notif-bell-btn:hover {
            background: #F8FAFC;
            color: var(--text-primary);
            border-color: #CBD5E1;
        }

        .notif-bell-btn .notif-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #EF4444;
            border: 1.5px solid var(--surface);
        }

        .notif-bell-btn .notif-badge.pulsing {
            background: #10B981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-badge 1.8s infinite;
        }

        @keyframes pulse-badge {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .notif-dd-panel {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 330px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.14), 0 2px 6px rgba(15, 23, 42, 0.06);
            z-index: 100;
            display: none;
            overflow: hidden;
        }

        .notif-dd-panel.open {
            display: block;
        }

        .notif-dd-panel::before {
            content: "";
            position: absolute;
            top: -6px;
            right: 14px;
            width: 12px;
            height: 12px;
            background: var(--surface);
            border-left: 1px solid var(--border);
            border-top: 1px solid var(--border);
            transform: rotate(45deg);
        }

        .notif-dd-head {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            background: #F8FAFC;
        }

        .notif-dd-list {
            max-height: 320px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 12px 16px;
            border-bottom: 1px solid #F1F5F9;
            transition: background 0.15s ease;
        }

        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-item:hover {
            background: #F8FAFC;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-logout:hover {
            border-color: #FCA5A5;
            color: #B91C1C;
            background: #FEF2F2;
        }

        .btn-logout.icon-only {
            padding: 7px;
            width: 34px;
            height: 34px;
            justify-content: center;
        }

        .btn-logout.icon-only span {
            display: none;
        }

        /* ============== MAIN ============== */
        .app-main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 28px 32px;
            flex: 1;
            width: 100%;
        }

        /* ============== CARDS ============== */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--shadow-card);
        }

        .card-hover {
            transition: transform .22s cubic-bezier(.22, 1, .36, 1), box-shadow .22s ease, border-color .22s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-200);
        }

        /* ============== BUTTONS ============== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 0;
            border-radius: 10px;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: background .18s ease, color .18s ease, border-color .18s ease, box-shadow .2s ease, transform .2s ease;
            padding: 10px 18px;
            font-size: 15px;
            line-height: 1.2;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn:active {
            transform: translateY(0) scale(.98);
        }

        /* Arrow that shifts right on hover — for buttons with → */
        .btn .arrow {
            display: inline-block;
            transition: transform .2s ease;
        }

        .btn:hover .arrow {
            transform: translateX(3px);
        }

        .btn-primary {
            background: var(--primary-500);
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .22);
            position: relative;
            overflow: hidden;
        }

        /* Subtle gradient shine that drifts on hover */
        .btn-primary::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 30%, rgba(255, 255, 255, .18) 50%, transparent 70%);
            transform: translateX(-110%);
            transition: transform .55s ease;
            pointer-events: none;
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
            color: #fff !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(37, 99, 235, .4), 0 0 0 4px rgba(37, 99, 235, .08);
        }

        .btn-primary:hover:not(:disabled)::before {
            transform: translateX(110%);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(37, 99, 235, .25);
        }

        .btn-secondary {
            background: var(--surface);
            color: #334155;
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--primary-50);
            color: var(--primary-600);
            border-color: var(--primary-200);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: transparent;
            color: var(--primary-500);
            padding: 10px 14px;
        }

        .btn-ghost:hover {
            background: var(--primary-50);
            color: var(--primary-700);
            text-decoration: none;
        }

        .btn-danger {
            background: #FEE2E2;
            color: #B91C1C;
        }

        .btn-danger:hover {
            background: #FECACA;
        }

        /* ============== ALERTS ============== */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 15px;
            margin-bottom: 18px;
            border: 1px solid;
        }

        .alert-success {
            background: #ECFDF5;
            color: #065F46;
            border-color: #A7F3D0;
        }

        .alert-danger {
            background: #FEF2F2;
            color: #991B1B;
            border-color: #FECACA;
        }

        .alert ul {
            margin: 0;
            padding-left: 18px;
        }

        /* ============== SWEETALERT2 MODERN THEME ============== */
        .swal2-container {
            z-index: 99999 !important;
        }

        .swal2-container.swal2-backdrop-show:not(.swal2-top-end):not(:has(.swal2-toast)) {
            background: rgba(15, 23, 42, 0.45) !important;
            backdrop-filter: blur(5px) !important;
            -webkit-backdrop-filter: blur(5px) !important;
        }

        body.swal2-toast-shown .swal2-container,
        .swal2-container:has(.swal2-toast),
        .swal2-container.swal2-top-end {
            background: transparent !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            pointer-events: none !important;
        }

        body.swal2-toast-shown .swal2-popup {
            pointer-events: auto !important;
        }

        .swal2-popup.swal2-modern-modal {
            border-radius: 20px !important;
            padding: 26px 22px 22px !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(226, 232, 240, 0.9) !important;
            background: #FFFFFF !important;
            font-family: inherit !important;
            width: 92% !important;
            max-width: 440px !important;
            border: none !important;
        }

        .swal2-modern-title {
            font-size: 18px !important;
            font-weight: 800 !important;
            color: var(--text-primary) !important;
            padding: 0 !important;
            margin: 14px 0 6px !important;
            line-height: 1.3 !important;
            letter-spacing: -0.01em !important;
        }

        .swal2-modern-html {
            font-size: 13.5px !important;
            color: var(--text-secondary) !important;
            margin: 0 !important;
            line-height: 1.55 !important;
        }

        .swal2-modern-actions {
            margin: 20px 0 0 !important;
            gap: 10px !important;
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
        }

        .swal2-modern-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 10px 20px !important;
            border-radius: 10px !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            font-family: inherit !important;
            cursor: pointer !important;
            border: 0 !important;
            transition: all .15s ease !important;
            min-height: 40px !important;
        }

        .swal2-modern-btn.btn-confirm-primary {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600)) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
        }

        .swal2-modern-btn.btn-confirm-primary:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35) !important;
        }

        .swal2-modern-btn.btn-confirm-danger {
            background: linear-gradient(135deg, #EF4444, #DC2626) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25) !important;
        }

        .swal2-modern-btn.btn-confirm-danger:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35) !important;
        }

        .swal2-modern-btn.btn-confirm-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25) !important;
        }

        .swal2-modern-btn.btn-cancel-neutral {
            background: #F8FAFC !important;
            color: var(--text-secondary) !important;
            border: 1px solid var(--border) !important;
        }

        .swal2-modern-btn.btn-cancel-neutral:hover {
            background: #F1F5F9 !important;
            color: var(--text-primary) !important;
        }

        /* Modern Toast Styling */
        .swal2-popup.swal2-modern-toast {
            border-radius: 14px !important;
            padding: 12px 18px !important;
            background: #FFFFFF !important;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.14), 0 0 0 1px rgba(226, 232, 240, 0.8) !important;
            border-left: 4px solid var(--primary-500) !important;
            display: flex !important;
            align-items: center !important;
        }

        .swal2-popup.swal2-modern-toast.swal2-toast-success {
            border-left-color: #10B981 !important;
        }

        .swal2-popup.swal2-modern-toast.swal2-toast-error {
            border-left-color: #EF4444 !important;
        }

        .swal2-popup.swal2-modern-toast.swal2-toast-warning {
            border-left-color: #F59E0B !important;
        }

        .swal2-modern-toast-title {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            margin: 0 !important;
            padding: 0 0 0 8px !important;
        }

        /* ============== TABLES ============== */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        table th {
            text-align: left;
            padding: 14px 16px;
            background: var(--background);
            color: var(--text-secondary);
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 1px solid var(--border);
        }

        table td {
            padding: 16px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            height: 56px;
        }

        table tbody tr {
            transition: background .15s ease;
        }

        table tbody tr:hover {
            background: var(--background);
        }

        table tr:last-child td {
            border-bottom: 0;
        }

        /* ============== BADGES ============== */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .badge-open {
            background: var(--primary-50);
            color: var(--primary-600);
        }

        .badge-closing {
            background: #ECFDF5;
            color: #047857;
        }

        .badge-cancel {
            background: #FEE2E2;
            color: #B91C1C;
        }

        /* ============== FORM ============== */
        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
            color: var(--text-primary);
            background: var(--surface);
            transition: border-color .15s, box-shadow .15s;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: 0;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 720px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        /* ============== PAGE HEADER ============== */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 29px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
            color: var(--text-primary);
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 15px;
            margin: 4px 0 0;
        }

        /* ============== FOOTER ============== */
        .app-footer {
            position: relative;
            background: var(--surface);
            border-top: 1px solid var(--border);
            color: var(--text-secondary);
            margin-top: auto;
            overflow: hidden;
        }

        .app-footer::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 1px 1px, rgba(148, 163, 184, .12) 1px, transparent 0);
            background-size: 28px 28px;
            pointer-events: none;
            mask-image: linear-gradient(180deg, black 0%, transparent 80%);
            -webkit-mask-image: linear-gradient(180deg, black 0%, transparent 80%);
        }

        .app-footer-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 28px;
            position: relative;
        }

        /* Main grid — 3 kolom (brand | nav | bantuan) */
        .app-footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr;
            gap: 48px;
            padding: 48px 0 28px;
        }

        .app-footer h4 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--text-primary);
            margin: 0 0 18px;
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .app-footer h4::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 24px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-500), transparent);
            border-radius: 2px;
        }

        .app-footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .app-footer li {
            margin-bottom: 12px;
            font-size: 14px;
        }

        .app-footer a {
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color .18s ease;
            position: relative;
            padding-left: 0;
        }

        .app-footer a::before {
            content: "›";
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1;
            transition: transform .18s ease, color .18s ease;
        }

        .app-footer a:hover {
            color: var(--primary-600);
            text-decoration: none;
        }

        .app-footer a:hover::before {
            color: var(--primary-600);
            transform: translateX(3px);
        }

        /* Brand block */
        .brand-block .footer-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .brand-block .footer-logo img {
            height: 36px;
            width: auto;
            filter: none;
        }

        .brand-block .footer-logo .badge-mini {
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--primary-600);
            background: var(--primary-50);
            padding: 4px 9px;
            border-radius: 6px;
            border: 1px solid var(--primary-200);
        }

        .brand-block p.tagline {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.65;
            margin: 0 0 20px;
            max-width: 360px;
        }

        /* Contact info */
        .footer-contact {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .contact-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .contact-item .ic-box {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--primary-50);
            border: 1px solid var(--primary-100);
            color: var(--primary-600);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .contact-item a {
            padding: 0;
            color: var(--text-secondary);
        }

        .contact-item a:hover {
            color: var(--primary-600);
        }

        .contact-item a::before {
            display: none;
        }

        /* Bottom bar */
        .app-footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0 24px;
            border-top: 1px solid var(--border);
            font-size: 13.5px;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 14px;
        }

        .app-footer-bottom .copy strong {
            color: var(--text-primary);
            font-weight: 700;
        }

        .app-footer-bottom .legal {
            display: flex;
            gap: 22px;
        }

        .app-footer-bottom .legal a {
            color: var(--text-muted);
            font-size: 13.5px;
            transition: color .15s ease;
        }

        .app-footer-bottom .legal a::before {
            display: none;
        }

        .app-footer-bottom .legal a:hover {
            color: var(--primary-600);
        }

        .app-footer-bottom .credit {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* ============== RESPONSIVE ============== */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: 0;
            cursor: pointer;
            padding: 8px;
        }

        /* Default: drawer & overlay hidden on desktop */
        .mobile-drawer {
            display: none;
        }

        .mobile-overlay {
            display: none;
        }

        @media (max-width: 900px) {

            /* Hide primary nav (use mobile menu instead) */
            .app-nav {
                display: none;
            }

            .mobile-menu-btn {
                display: inline-flex;
            }

            /* Mobile drawer (right slide-in) */
            .mobile-drawer {
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                width: 320px;
                max-width: 90vw;
                background: var(--surface);
                box-shadow: -10px 0 32px rgba(15, 23, 42, .14);
                z-index: 200;
                transform: translateX(100%);
                transition: transform .25s ease;
            }

            .mobile-drawer.open {
                transform: translateX(0);
            }

            .mobile-drawer-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 20px;
                border-bottom: 1px solid var(--border);
                flex-shrink: 0;
            }

            .mobile-drawer-head .ttl {
                font-size: 15px;
                font-weight: 700;
                color: var(--text-primary);
            }

            .mobile-drawer-close {
                background: transparent;
                border: 0;
                cursor: pointer;
                padding: 6px;
                color: var(--text-secondary);
            }

            .mobile-drawer-body {
                flex: 1;
                overflow-y: auto;
                padding: 12px 16px 20px;
            }

            .mobile-drawer .nav-section {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .08em;
                color: var(--text-muted);
                padding: 14px 12px 6px;
            }

            .mobile-drawer .nav-section:first-child {
                padding-top: 4px;
            }

            .mobile-drawer a.nav-link,
            .mobile-drawer a.nav-dd-item {
                padding: 11px 12px;
                margin-bottom: 2px;
            }

            .mobile-drawer a.nav-link.active::after {
                display: none;
            }

            .mobile-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, .4);
                z-index: 150;
                opacity: 0;
                pointer-events: none;
                transition: opacity .25s ease;
            }

            .mobile-overlay.open {
                opacity: 1;
                pointer-events: auto;
            }

            .role-pill {
                display: none;
            }

            .user-name {
                display: none;
            }

            .user-avatar {
                display: inline-flex;
            }

            .btn-logout {
                display: none;
            }
        }

        @media (max-width: 1100px) {
            .user-name {
                display: none;
            }

            .btn-logout>span {
                display: none;
            }

            .btn-logout {
                padding: 7px;
                width: 34px;
                height: 34px;
                justify-content: center;
            }
        }

        @media (max-width: 720px) {
            .app-main {
                padding: 22px 16px 32px;
            }

            .app-header-inner {
                padding: 0 16px;
            }

            .app-footer-inner {
                padding: 0 20px;
            }

            .app-footer-grid {
                grid-template-columns: 1fr;
                gap: 28px;
                padding: 32px 0;
            }

            .footer-cta {
                flex-direction: column;
                align-items: stretch;
                padding: 24px 0;
            }

            .footer-cta-form {
                min-width: 0;
            }

            .app-footer-bottom {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ============== DASHBOARD HERO ============== */
        .hero {
            display: grid;
            grid-template-columns: 58% 42%;
            gap: 32px;
            align-items: center;
            padding: 32px 0 40px;
            animation: heroIn 360ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .hero-greet {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .hero-title {
            font-size: 34px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-primary);
            margin: 0 0 12px;
            line-height: 1.15;
        }

        .hero-desc {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin: 0 0 20px;
            max-width: 480px;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 4px;
        }

        .hero-actions .btn {
            padding: 11px 22px;
            font-size: 15px;
            border-radius: 11px;
        }

        .hero-illustration {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-illustration svg {
            width: 100%;
            max-width: 360px;
            height: auto;
        }

        @media (max-width: 900px) {
            .hero {
                grid-template-columns: 1fr;
                gap: 18px;
                padding: 22px 0 28px;
            }

            .hero-title {
                font-size: 29px;
            }
        }

        @keyframes heroIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============== KPI GRID ============== */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .kpi-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .kpi-card {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-height: 130px;
            position: relative;
        }

        /* Make link-KPIs feel clickable */
        a.kpi-card::after {
            content: "→";
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 15px;
            color: var(--text-muted);
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity .22s ease, transform .22s ease, color .22s ease;
        }

        a.kpi-card:hover::after {
            opacity: 1;
            transform: translateX(0);
            color: var(--primary-600);
        }

        .kpi-card-link .kpi-meta::after {
            content: " →";
            display: inline-block;
            transition: transform .2s ease;
        }

        .kpi-card-link:hover .kpi-meta::after {
            transform: translateX(3px);
        }

        .kpi-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
        }

        .kpi-label {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .kpi-value {
            font-size: 29px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.1;
            letter-spacing: -0.02em;
            margin-top: 2px;
        }

        .kpi-meta {
            font-size: 15px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .kpi-meta-action {
            color: var(--primary-600);
            font-weight: 600;
        }

        @media (max-width: 1023px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .kpi-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }

        .kpi-card:nth-child(1) {
            animation: kpiIn 360ms 50ms cubic-bezier(0.22, 1, 0.36, 1) backwards;
        }

        .kpi-card:nth-child(2) {
            animation: kpiIn 360ms 110ms cubic-bezier(0.22, 1, 0.36, 1) backwards;
        }

        .kpi-card:nth-child(3) {
            animation: kpiIn 360ms 170ms cubic-bezier(0.22, 1, 0.36, 1) backwards;
        }

        .kpi-card:nth-child(4) {
            animation: kpiIn 360ms 230ms cubic-bezier(0.22, 1, 0.36, 1) backwards;
        }

        @keyframes kpiIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============== SECTION ============== */
        .prospect-section {
            margin-bottom: 24px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .section-header>div:first-child {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--primary-50);
            color: var(--primary-600);
            margin-bottom: 6px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }

        .section-desc {
            font-size: 15px;
            color: var(--text-secondary);
            margin: 2px 0 0;
        }

        /* ============== QUICK GRID (manager) ============== */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .quick-card {
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
            min-height: 120px;
            position: relative;
        }

        .quick-card h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 10px 0 4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .quick-card p {
            font-size: 15px;
            color: var(--text-secondary);
            margin: 0;
        }

        .quick-card::after {
            content: "→";
            position: absolute;
            top: 16px;
            right: 18px;
            font-size: 15px;
            color: var(--text-muted);
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity .22s ease, transform .22s ease, color .22s ease;
        }

        .quick-card:hover::after {
            opacity: 1;
            transform: translateX(0);
            color: var(--primary-600);
        }

        .quick-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ============== 2K / ULTRA-WIDE SCREEN SCALING (1440p / 2560px+) ============== */
        @media (min-width: 1440px) {
            .app-header-inner {
                max-width: 1400px;
            }

            .app-main {
                max-width: 1400px;
            }

            .app-footer-inner {
                max-width: 1400px;
            }
        }

        @media (min-width: 1920px) {
            html {
                font-size: 16px;
            }

            .app-header {
                height: 84px;
            }

            .app-header-inner {
                max-width: 1720px;
                padding: 0 36px;
            }

            .app-header .brand img {
                height: 48px;
            }

            .nav-link,
            .nav-dd-toggle {
                font-size: 16px;
                padding: 8px 14px;
            }

            .app-main {
                max-width: 1720px;
                padding: 40px 36px 48px;
            }

            .card {
                padding: 26px;
                border-radius: 20px;
            }

            .page-header h1 {
                font-size: 34px;
            }

            .page-header p {
                font-size: 16.5px;
            }

            .btn {
                padding: 12px 22px;
                font-size: 16px;
                border-radius: 12px;
            }

            table th {
                padding: 14px 18px;
                font-size: 12.5px;
            }

            table td {
                padding: 16px 18px;
                font-size: 15px;
            }

            .field label {
                font-size: 14.5px;
            }

            .field input,
            .field select,
            .field textarea {
                padding: 12px 14px;
                font-size: 15px;
                border-radius: 12px;
            }

            .app-footer-inner {
                max-width: 1720px;
                padding: 0 36px;
            }

            .app-footer-grid {
                gap: 64px;
                padding: 56px 0 32px;
            }

            .app-footer h4 {
                font-size: 15px;
            }

            .brand-block p.tagline,
            .footer-contact,
            .app-footer-links a {
                font-size: 15px;
            }
        }

        @media (min-width: 2400px) {
            html {
                font-size: 17px;
            }

            .app-header {
                height: 90px;
            }

            .app-header-inner {
                max-width: 2160px;
                padding: 0 48px;
            }

            .app-main {
                max-width: 2160px;
                padding: 48px 48px 60px;
            }

            .app-footer-inner {
                max-width: 2160px;
                padding: 0 48px;
            }

            .card {
                padding: 30px;
                border-radius: 22px;
            }

            .page-header h1 {
                font-size: 38px;
            }
        }

        /* ============== GLOBAL PAGINATION STYLES ============== */
        .table-pagination {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            background: #FAFBFC;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hf-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .hf-pagination-info {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .hf-pagination-highlight {
            font-weight: 700;
            color: var(--text-primary);
        }

        .hf-pagination-links {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .hf-page-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: #FFFFFF;
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all .15s ease;
            user-select: none;
            box-sizing: border-box;
            line-height: 1;
        }

        .hf-page-item:hover:not(.hf-page-disabled):not(.hf-page-active):not(.hf-page-dots) {
            background: var(--primary-50);
            color: var(--primary-700);
            border-color: var(--primary-200);
            text-decoration: none;
        }

        .hf-page-item.hf-page-active {
            background: var(--primary-600);
            color: #FFFFFF;
            border-color: var(--primary-600);
            font-weight: 700;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.22);
            cursor: default;
        }

        .hf-page-item.hf-page-disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #F8FAFC;
            border-color: #E2E8F0;
            color: #94A3B8;
            pointer-events: none;
        }

        .hf-page-item.hf-page-dots {
            border: none;
            background: transparent;
            cursor: default;
            color: #94A3B8;
            min-width: 22px;
            padding: 0 4px;
        }

        .hf-page-item svg {
            width: 16px;
            height: 16px;
            display: inline-block;
            vertical-align: middle;
            stroke: currentColor;
            flex-shrink: 0;
        }

        @media (max-width: 640px) {
            .hf-pagination {
                flex-direction: column;
                align-items: center;
                gap: 10px;
                text-align: center;
            }
            .hf-pagination-info {
                text-align: center;
                width: 100%;
            }
            .hf-pagination-links {
                justify-content: center;
                width: 100%;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <header class="app-header">
        <div class="app-header-inner">
            <a href="{{ route('dashboard') }}" class="brand" aria-label="HIVEFIVE">
                <img src="{{ asset('assets/logohv.png') }}" alt="HIVEFIVE">
            </a>

            <nav class="app-nav" id="appNav" aria-label="Main navigation">
                @auth
                @php $role = auth()->user()->role?->slug; @endphp

                {{-- Primary items always visible --}}
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                @if ($role === 'marketing')
                <a href="{{ route('prospects.index') }}" class="nav-link {{ request()->routeIs('prospects.*') ? 'active' : '' }}" title="Prospek Saya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span>Prospek Saya</span>
                </a>
                <a href="{{ route('todos.daily') }}" class="nav-link {{ request()->routeIs('todos.daily*') ? 'active' : '' }}" title="To Do Harian">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span>To Do Harian</span>
                </a>
                @endif

                @if (in_array($role, ['cs','manager_marketing','super_admin']))
                <a href="{{ route('prospects.index') }}" class="nav-link {{ request()->routeIs('prospects.*') ? 'active' : '' }}" title="Prospek">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span>Prospek</span>
                </a>
                @endif

                @if (in_array($role, ['manager_marketing','super_admin']))
                <a href="{{ route('manager.todos') }}" class="nav-link {{ request()->routeIs('manager.todos*') ? 'active' : '' }}" title="Monitor To Do">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                    <span>Monitor To Do</span>
                </a>
                @endif

                {{-- Admin dropdown --}}
                @if ($role === 'super_admin')
                @php
                $adminActive = request()->routeIs('admin.*');
                @endphp
                <div class="nav-dd" id="adm-dd">
                    <button type="button" class="nav-dd-toggle {{ $adminActive ? 'active-cat' : '' }}" data-dd="adm-dd">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1.82.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                        </svg>
                        <span>Admin</span>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </button>
                    <div class="nav-dd-panel" id="adm-dd-panel">
                        <a href="{{ route('admin.users.index') }}" class="nav-dd-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Users
                        </a>
                        <a href="{{ route('admin.masters.index') }}" class="nav-dd-item {{ request()->routeIs('admin.masters*') ? 'active' : '' }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <ellipse cx="12" cy="5" rx="9" ry="3" />
                                <path d="M3 5v14a9 3 0 0 0 18 0V5" />
                            </svg>
                            Master Data
                        </a>
                    </div>
                </div>
                @endif
                @endauth
            </nav>

            <div class="app-user">
                @auth
                @if(in_array(auth()->user()->role?->slug, ['manager_marketing', 'super_admin']))
                <div class="notif-dd-wrap" id="export-notif-dd">
                    <button type="button" class="notif-bell-btn" id="exportNotifBtn" title="Notifikasi Export Background" aria-haspopup="true" aria-expanded="false">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notif-badge" id="exportNotifBadge" style="display: none;"></span>
                    </button>
                    <div class="notif-dd-panel" id="exportNotifPanel" role="menu">
                        <div class="notif-dd-head">
                            <div style="font-weight: 800; font-size: 13.5px; color: var(--text-primary); display: flex; align-items: center; justify-content: space-between;">
                                <span>Export To-Do (.ZIP)</span>
                                <span id="exportNotifCountBadge" style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Background Job</span>
                            </div>
                            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                File export Excel otomatis di latar belakang
                            </div>
                        </div>
                        <div class="notif-dd-list" id="exportNotifList">
                            <div style="text-align: center; padding: 22px 12px; color: var(--text-muted); font-size: 12.5px;">
                                Memuat status...
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="user-dd-wrap" id="user-dd">
                    <button type="button" class="user-avatar" data-udd-toggle aria-haspopup="true" aria-expanded="false" title="{{ auth()->user()->name }}">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</button>
                    <div class="user-dd-panel" role="menu">
                        <div class="user-dd-head">
                            <div class="user-dd-name">{{ auth()->user()->name }}</div>
                            <div class="user-dd-meta">
                                <span class="role-chip">{{ str_replace('_',' ', auth()->user()->role?->name ?? '-') }}</span>
                                <span>{{ auth()->user()->email ?? auth()->user()->username ?? '' }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="user-dd-item" role="menuitem">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" y1="12" x2="9" y2="12" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-logout icon-only" title="Logout">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                    </button>
                </form>
                <button class="mobile-menu-btn" type="button" aria-label="Toggle menu" onclick="document.getElementById('mobileDrawer').classList.add('open'); document.getElementById('mobileOverlay').classList.add('open');">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <line x1="3" y1="12" x2="21" y2="12" />
                        <line x1="3" y1="18" x2="21" y2="18" />
                    </svg>
                </button>
                @endauth
            </div>
        </div>
    </header>

    {{-- Mobile drawer (slide from right) --}}
    <div class="mobile-overlay" id="mobileOverlay" onclick="document.getElementById('mobileDrawer').classList.remove('open'); this.classList.remove('open');"></div>
    <aside class="mobile-drawer" id="mobileDrawer" aria-label="Mobile menu">
        <div class="mobile-drawer-head">
            <span class="ttl">Menu</span>
            <button type="button" class="mobile-drawer-close" aria-label="Close" onclick="document.getElementById('mobileDrawer').classList.remove('open'); document.getElementById('mobileOverlay').classList.remove('open');">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <div class="mobile-drawer-body">
            @auth
            @php $role = auth()->user()->role?->slug; @endphp
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                Dashboard
            </a>
            @if ($role === 'marketing')
            <a href="{{ route('prospects.index') }}" class="nav-link {{ request()->routeIs('prospects.*') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                Prospek Saya
            </a>
            <a href="{{ route('todos.daily') }}" class="nav-link {{ request()->routeIs('todos.daily*') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                To Do Harian
            </a>
            @endif
            @if (in_array($role, ['cs','manager_marketing','super_admin']))
            <a href="{{ route('prospects.index') }}" class="nav-link {{ request()->routeIs('prospects.*') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                Prospek
            </a>
            @endif
            @if (in_array($role, ['manager_marketing','super_admin']))
            <a href="{{ route('manager.todos') }}" class="nav-link {{ request()->routeIs('manager.todos*') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4" />
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                </svg>
                Monitor To Do
            </a>
            @endif
            @if ($role === 'super_admin')
            <div class="nav-section">Admin</div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a>
            <a href="{{ route('admin.masters.index') }}" class="nav-link {{ request()->routeIs('admin.masters*') ? 'active' : '' }}">Master Data</a>
            @endif
            <div class="nav-section">Akun</div>
            <div style="display:flex; align-items:center; gap:10px; padding: 8px 12px;">
                <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <div style="min-width:0;">
                    <div style="font-weight:600; font-size: 14px; color:var(--text-primary);">{{ auth()->user()->name }}</div>
                    <div class="role-pill" style="display:inline-block; margin-top:2px;">{{ str_replace('_',' ', auth()->user()->role?->name ?? '-') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:8px 12px 0;">
                @csrf
                <button type="submit" class="btn-logout" style="width:100%; justify-content:center;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
            @endauth
        </div>
    </aside>

    <main class="app-main">
        @php
        $hasErrors = isset($errors) && $errors->any() && !request()->routeIs('login');
        @endphp
        @if (session('status') || session('success') || session('error') || $hasErrors)
        <div id="app-flash-messages" style="display:none;"
            data-status="{{ session('status') ?? session('success') }}"
            data-error="{{ session('error') }}"
            data-errors="{{ $hasErrors ? json_encode($errors->all()) : '' }}">
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer">
        <div class="app-footer-inner">

            <div class="app-footer-grid">

                <div class="brand-block">
                    <div class="footer-logo">
                        <img src="{{ asset('assets/logohv.png') }}" alt="HIVEFIVE">
                    </div>
                    <p class="tagline">HIVEFIVE Prospect System — platform manajemen prospek modern untuk tim marketing yang ingin hasil lebih cepat dan terukur.</p>

                    <div class="footer-contact">
                        <div class="contact-item">
                            <span class="ic-box">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </span>
                            <a href="mailto:admin@prospek.local">admin@prospek.local</a>
                        </div>
                        <div class="contact-item">
                            <span class="ic-box">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                            </span>
                            <span>+62 859-2458-5391</span>
                        </div>
                        <div class="contact-item">
                            <span class="ic-box">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </span>
                            <span>18 Office Park Building Lantai 21 Unit C
                                <br />
                                Jl. TB Simatupang No.18 Kel. Kebagusan, Kec. Pasar Minggu <br /> Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12520</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4>Navigasi</h4>
                    <ul>
                        @auth
                        @php $role = auth()->user()->role?->slug; @endphp
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        @if ($role === 'marketing')
                        <li><a href="{{ route('prospects.index') }}">Prospek Saya</a></li>
                        <li><a href="{{ route('todos.daily') }}">To Do Harian</a></li>
                        @else
                        <li><a href="{{ route('prospects.index') }}">Prospek</a></li>
                        @if (in_array($role, ['manager_marketing','super_admin']))
                        <li><a href="{{ route('manager.todos') }}">Monitor To Do</a></li>
                        @endif
                        @if ($role === 'super_admin')
                        <li><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li><a href="{{ route('admin.masters.index') }}">Master Data</a></li>
                        @endif
                        @endif
                        @endauth
                    </ul>
                </div>

                <div>
                    <h4>Bantuan</h4>
                    <ul>
                        <li><a href="https://wa.me/+6285924585391">Kontak Support</a></li>
                    </ul>
                </div>

            </div>

            <div class="app-footer-bottom">
                <span class="copy">&copy; {{ date('Y') }} <strong>HIVEFIVE Prospect System</strong>. All rights reserved.</span>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.nav-dd-toggle').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var key = btn.getAttribute('data-dd');
                    var panel = document.getElementById(key + '-panel');
                    if (!panel) return;
                    var isOpen = panel.classList.contains('open');
                    document.querySelectorAll('.nav-dd-panel.open').forEach(function(p) {
                        p.classList.remove('open');
                    });
                    document.querySelectorAll('.nav-dd-toggle.active-cat').forEach(function(t) {
                        t.classList.remove('active-cat');
                    });
                    if (!isOpen) {
                        panel.classList.add('open');
                        btn.classList.add('active-cat');
                    }
                });
            });
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-dd')) {
                    document.querySelectorAll('.nav-dd-panel.open').forEach(function(p) {
                        p.classList.remove('open');
                    });
                    document.querySelectorAll('.nav-dd-toggle.active-cat').forEach(function(t) {
                        t.classList.remove('active-cat');
                    });
                }
                if (!e.target.closest('#user-dd')) {
                    var ud = document.querySelector('#user-dd .user-dd-panel');
                    if (ud) ud.classList.remove('open');
                    var ub = document.querySelector('[data-udd-toggle]');
                    if (ub) ub.setAttribute('aria-expanded', 'false');
                }
                if (!e.target.closest('#export-notif-dd')) {
                    var nPanel = document.getElementById('exportNotifPanel');
                    if (nPanel) nPanel.classList.remove('open');
                    var nBtn = document.getElementById('exportNotifBtn');
                    if (nBtn) nBtn.setAttribute('aria-expanded', 'false');
                }
            });
            var uddBtn = document.querySelector('[data-udd-toggle]');
            if (uddBtn) {
                uddBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var panel = document.querySelector('#user-dd .user-dd-panel');
                    var open = panel.classList.toggle('open');
                    uddBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                    var nPanel = document.getElementById('exportNotifPanel');
                    if (nPanel) nPanel.classList.remove('open');
                });
            }

            // Export Notification Dropdown & Polling
            var notifBtn = document.getElementById('exportNotifBtn');
            var notifPanel = document.getElementById('exportNotifPanel');
            var notifBadge = document.getElementById('exportNotifBadge');
            var notifList = document.getElementById('exportNotifList');
            var notifPollTimer = null;
            var notifiedCompletedIds = {};
            try {
                notifiedCompletedIds = JSON.parse(sessionStorage.getItem('notified_export_ids') || '{}');
            } catch (err) {}

            function renderExportNotifications(exports) {
                if (!notifList) return;
                if (!exports || exports.length === 0) {
                    notifList.innerHTML = '<div style="text-align:center; padding:24px 12px; color:var(--text-muted); font-size:12.5px;">Belum ada riwayat export.</div>';
                    return;
                }

                var html = '';
                var hasActive = false;

                exports.forEach(function(item) {
                    var isCompleted = item.status === 'completed';
                    var isProcessing = item.status === 'processing' || item.status === 'pending';
                    var isFailed = item.status === 'failed';

                    if (isProcessing) hasActive = true;

                    if (isCompleted && !notifiedCompletedIds[item.id]) {
                        notifiedCompletedIds[item.id] = true;
                        try { sessionStorage.setItem('notified_export_ids', JSON.stringify(notifiedCompletedIds)); } catch(e){}
                        if (window.AppSwal && AppSwal.toast) {
                            AppSwal.toast('success', 'Export to-do ' + (item.month_label || '') + ' selesai! Klik lonceng untuk mengunduh.');
                        }
                    }

                    html += '<div class="notif-item">';
                    html += '  <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:4px;">';
                    html += '    <div style="font-weight:700; font-size:13px; color:var(--text-primary);">' + (item.month_label || 'Export To-Do') + '</div>';

                    if (isProcessing) {
                        html += '    <span style="display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; color:#2563EB; background:#EFF6FF; padding:2px 8px; border-radius:999px;">';
                        html += '      <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#2563EB; animation:pulse-badge 1s infinite;"></span> Diproses';
                        html += '    </span>';
                    } else if (isCompleted) {
                        html += '    <span style="font-size:11px; font-weight:700; color:#059669; background:#ECFDF5; padding:2px 8px; border-radius:999px;">Selesai</span>';
                    } else {
                        html += '    <span style="font-size:11px; font-weight:700; color:#DC2626; background:#FEF2F2; padding:2px 8px; border-radius:999px;">Gagal</span>';
                    }
                    html += '  </div>';

                    if (isProcessing) {
                        html += '  <div style="font-size:11.5px; color:var(--text-secondary); margin-bottom:6px;">' + item.processed + ' dari ' + item.total + ' marketing (' + item.percent + '%)</div>';
                        html += '  <div style="width:100%; height:5px; background:#E2E8F0; border-radius:999px; overflow:hidden;">';
                        html += '    <div style="width:' + item.percent + '%; height:100%; background:linear-gradient(90deg, #3B82F6, #2563EB); border-radius:999px; transition:width .2s;"></div>';
                        html += '  </div>';
                    } else if (isCompleted) {
                        html += '  <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">';
                        html += '    <span style="font-size:11px; color:var(--text-muted);">' + (item.time_ago || '') + '</span>';
                        html += '    <a href="' + item.download_url + '" class="btn btn-sm btn-primary" style="padding:4px 10px; font-size:11.5px; font-weight:700; display:inline-flex; align-items:center; gap:4px; text-decoration:none; border-radius:8px;">';
                        html += '      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>';
                        html += '      Unduh ZIP';
                        html += '    </a>';
                        html += '  </div>';
                    } else {
                        html += '  <div style="font-size:11px; color:#EF4444; margin-top:2px;">Terjadi kesalahan saat memproses file.</div>';
                    }
                    html += '</div>';
                });

                notifList.innerHTML = html;

                if (notifBadge) {
                    if (hasActive) {
                        notifBadge.className = 'notif-badge pulsing';
                        notifBadge.style.display = 'block';
                    } else {
                        var hasRecentCompleted = exports.some(function(x) { return x.status === 'completed'; });
                        if (hasRecentCompleted) {
                            notifBadge.className = 'notif-badge';
                            notifBadge.style.display = 'block';
                        } else {
                            notifBadge.style.display = 'none';
                        }
                    }
                }

                return hasActive;
            }

            function fetchExportNotifications(keepPolling) {
                if (!notifBtn) return;
                fetch('{{ route("manager.todos.export.recent", [], false) }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    var hasActive = renderExportNotifications(data.exports || []);
                    if (notifPollTimer) clearTimeout(notifPollTimer);
                    if (hasActive) {
                        try { sessionStorage.setItem('has_active_export', '1'); } catch (e) {}
                        notifPollTimer = setTimeout(function() { fetchExportNotifications(true); }, 3000);
                    } else {
                        try { sessionStorage.removeItem('has_active_export'); } catch (e) {}
                        // Berhenti polling jika tidak ada export aktif
                    }
                })
                .catch(function() {
                    if (notifPollTimer) clearTimeout(notifPollTimer);
                    try { sessionStorage.removeItem('has_active_export'); } catch (e) {}
                });
            }

            if (notifBtn && notifPanel) {
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var open = notifPanel.classList.toggle('open');
                    notifBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                    var udPanel = document.querySelector('#user-dd .user-dd-panel');
                    if (udPanel) udPanel.classList.remove('open');
                    if (open) fetchExportNotifications(false);
                });

                // Hanya fetch otomatis saat awal buka jika ada antrean export aktif yang belum selesai
                try {
                    if (sessionStorage.getItem('has_active_export') === '1') {
                        fetchExportNotifications(true);
                    }
                } catch (e) {}
            }

            window.refreshExportNotifications = fetchExportNotifications;

            // Process flash messages with modern SweetAlert2
            var flashEl = document.getElementById('app-flash-messages');
            if (flashEl) {
                var statusMsg = flashEl.getAttribute('data-status');
                var errorMsg = flashEl.getAttribute('data-error');
                var rawErrors = flashEl.getAttribute('data-errors');

                if (statusMsg) {
                    AppSwal.toast('success', statusMsg);
                }
                if (errorMsg) {
                    AppSwal.error('Perhatian', errorMsg);
                }
                if (rawErrors) {
                    try {
                        var errors = JSON.parse(rawErrors);
                        if (Array.isArray(errors) && errors.length) {
                            AppSwal.fire({
                                icon: 'error',
                                title: 'Periksa Input Formulir',
                                html: '<ul style="text-align:left; margin:8px 0 0 16px; padding:0; font-size:13px; color:var(--text-secondary);">' +
                                    errors.map(function(e) {
                                        return '<li style="margin-bottom:4px;">' + e + '</li>';
                                    }).join('') +
                                    '</ul>',
                                confirmButtonText: 'Tutup',
                                isDanger: true
                            });
                        }
                    } catch (err) {}
                }
            }

            // Universal form confirmation interceptor
            document.querySelectorAll('form').forEach(function(form) {
                var onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.indexOf('confirm(') !== -1) {
                    var match = onsubmitAttr.match(/confirm\(\s*['"`]([\s\S]*?)['"`]\s*\)/);
                    if (match && match[1]) {
                        form.setAttribute('data-confirm', match[1].replace(/\\'/g, "'").replace(/\\"/g, '"'));
                        form.removeAttribute('onsubmit');
                    }
                }
            });

            document.addEventListener('submit', function(e) {
                var form = e.target;
                if (!form || !form.getAttribute) return;
                if (form.getAttribute('data-swal-confirmed') === 'true') {
                    form.removeAttribute('data-swal-confirmed');
                    return;
                }

                var confirmMsg = form.getAttribute('data-confirm');
                if (confirmMsg) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    var isDelete = form.querySelector('input[name="_method"][value="DELETE"]') ||
                        /hapus|delete|remove/i.test(confirmMsg);
                    var title = form.getAttribute('data-confirm-title') || (isDelete ? 'Konfirmasi Hapus' : 'Konfirmasi Tindakan');
                    var confirmBtn = form.getAttribute('data-confirm-btn') || (isDelete ? 'Ya, Hapus' : 'Ya, Lanjutkan');

                    AppSwal.confirm(title, confirmMsg, isDelete, confirmBtn).then(function(res) {
                        if (res.isConfirmed) {
                            form.setAttribute('data-swal-confirmed', 'true');
                            form.submit();
                        }
                    });
                }
            }, true);
        });
    </script>
</body>

</html>