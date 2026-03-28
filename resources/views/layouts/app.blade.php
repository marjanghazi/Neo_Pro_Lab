<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') - NeoProLab</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Icons/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/Icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --navy: #0D1B2A;
            --navy-mid: #172535;
            --navy-light: #1E3044;
            --teal: #0EA5A0;
            --teal-dark: #0B8A86;
            --teal-light: rgba(14, 165, 160, 0.08);
            --teal-border: rgba(14, 165, 160, 0.2);
            --white: #FFFFFF;
            --bg: #F4F7FA;
            --surface: #FFFFFF;
            --border: #E4EAF0;
            --text-primary: #111827;
            --text-secondary: #4B5563;
            --text-muted: #9CA3AF;
            --success: #059669;
            --warning: #D97706;
            --danger: #DC2626;
            --info: #2563EB;
            --sidebar-width: 248px;
            --sidebar-collapsed-width: 64px;
            --header-height: 52px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; width: 100%; overflow: hidden; }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
            font-size: 13.5px;
            line-height: 1.5;
        }

        /* ─── Layout ─────────────────────────────────────────────── */
        .app-wrapper { display: flex; height: 100vh; width: 100vw; overflow: hidden; }

        /* ─── Sidebar Desktop ────────────────────────────────────── */
        .sidebar-desktop {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--navy);
            color: white;
            overflow-y: auto;
            flex-shrink: 0;
            position: relative;
            z-index: 30;
            transition: width 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            scrollbar-width: none;
        }
        .sidebar-desktop::-webkit-scrollbar { display: none; }
        .sidebar-desktop.collapsed { width: var(--sidebar-collapsed-width); }

        /* ─── Sidebar Mobile ─────────────────────────────────────── */
        .sidebar-mobile {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--navy); color: white;
            overflow-y: auto; z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            scrollbar-width: none;
        }
        .sidebar-mobile::-webkit-scrollbar { display: none; }
        .sidebar-mobile.open { transform: translateX(0); }

        .mobile-overlay {
            position: fixed; inset: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(3px);
            z-index: 40; display: none;
        }
        .mobile-overlay.active { display: block; }

        /* ─── Main Content ───────────────────────────────────────── */
        .main-content { flex: 1; display: flex; flex-direction: column; height: 100vh; overflow: hidden; background: var(--bg); }

        /* ─── Navbar ─────────────────────────────────────────────── */
        .navbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0; position: relative; z-index: 45;
            height: var(--header-height);
        }

        /* ─── Content Area ───────────────────────────────────────── */
        .content-wrapper { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 1.25rem; }
        .content-wrapper::-webkit-scrollbar { width: 3px; }
        .content-wrapper::-webkit-scrollbar-track { background: transparent; }
        .content-wrapper::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 3px; }
        .content-wrapper::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }

        @media (max-width: 640px) { .content-wrapper { padding: 0.875rem; } }

        .content-container { width: 100%; max-width: 100%; }

        @media (max-width: 767px) { .sidebar-desktop { display: none; } }
        @media (min-width: 768px) {
            .sidebar-mobile { display: none; }
            .mobile-overlay { display: none !important; }
        }

        /* ─── Sidebar Navigation Items ───────────────────────────── */
        .sidebar-item {
            color: rgba(255, 255, 255, 0.55);
            padding: 8px 12px;
            border-radius: 7px;
            margin: 1px 8px;
            transition: color 0.15s, background 0.15s;
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.8125rem;
            position: relative;
            white-space: nowrap;
            letter-spacing: 0.01em;
        }
        .sidebar-item i { width: 16px; text-align: center; font-size: 0.875rem; flex-shrink: 0; }
        .sidebar-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
        .sidebar-item.active {
            background: rgba(14, 165, 160, 0.15);
            color: var(--teal);
            border: 1px solid rgba(14, 165, 160, 0.18);
        }
        .sidebar-item.active i { color: var(--teal); }

        .collapsed .sidebar-item { padding: 9px; justify-content: center; gap: 0; margin: 1px 8px; }
        .collapsed .sidebar-item span { display: none; }
        .collapsed .sidebar-item i { width: auto; font-size: 1rem; }
        .collapsed .logo-text { display: none; }
        .collapsed .logo-section .flex { justify-content: center; }
        .collapsed .user-profile-section { justify-content: center; padding: 0.5rem; }
        .collapsed .user-info, .collapsed .status-dot { display: none; }

        /* ─── Desktop menu button ─────────────────────────────────── */
        .desktop-menu-btn { display: none; }
        @media (min-width: 768px) { .desktop-menu-btn { display: flex; } }

        /* ─── Cards ──────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        /* ─── Stat Card ──────────────────────────────────────────── */
        .stat-card { background: var(--surface); border-radius: 10px; border: 1px solid var(--border); }

        /* ─── Table ──────────────────────────────────────────────── */
        .table-container {
            background: var(--surface); border-radius: 10px; border: 1px solid var(--border);
            overflow-x: auto; -webkit-overflow-scrolling: touch;
        }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th {
            background: #F8FAFC; font-weight: 600; color: #6B7280;
            padding: 10px 16px; border-bottom: 1px solid var(--border);
            font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.06em;
            white-space: nowrap;
        }
        td { padding: 10px 16px; border-bottom: 1px solid #F1F5F9; color: var(--text-secondary); font-size: 0.8125rem; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #FAFBFC; }

        /* ─── Badges ─────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 8px; border-radius: 5px;
            font-size: 0.6875rem; font-weight: 600; gap: 4px; white-space: nowrap;
        }
        .badge-success { background: #DCFCE7; color: #15803D; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger  { background: #FEE2E2; color: #B91C1C; }
        .badge-info    { background: #DBEAFE; color: #1D4ED8; }
        .badge-primary { background: var(--teal-light); color: var(--teal-dark); border: 1px solid var(--teal-border); }
        .badge-gray    { background: #F3F4F6; color: #4B5563; }

        /* ─── Buttons ────────────────────────────────────────────── */
        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 7px 14px; background: var(--teal); color: white;
            border-radius: 7px; font-size: 0.8125rem; font-weight: 500;
            border: none; cursor: pointer; text-decoration: none;
            transition: background 0.15s, transform 0.1s;
        }
        .btn-primary:hover { background: var(--teal-dark); }

        .btn-secondary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 7px 14px; background: transparent; color: var(--text-secondary);
            border-radius: 7px; font-size: 0.8125rem; font-weight: 500;
            border: 1px solid var(--border); cursor: pointer; text-decoration: none;
            transition: background 0.15s, border-color 0.15s;
        }
        .btn-secondary:hover { background: #F8FAFC; border-color: #CBD5E1; }

        /* ─── Form Controls ──────────────────────────────────────── */
        input[type="text"], input[type="number"], input[type="email"],
        input[type="password"], select, textarea {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.8125rem;
        }

        /* ─── Notification Badge ─────────────────────────────────── */
        .notification-badge {
            position: absolute; top: -3px; right: -3px;
            background: var(--danger); color: white; border-radius: 50%;
            min-width: 16px; height: 16px; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            padding: 0 3px; border: 2px solid white;
        }

        /* ─── Logo ───────────────────────────────────────────────── */
        .logo-wrapper {
            background: rgba(255,255,255,0.1);
            border-radius: 8px; padding: 6px;
            border: 1px solid rgba(255,255,255,0.08);
        }

        /* ─── Dropdowns ──────────────────────────────────────────── */
        .dropdown-container { position: relative; }

        .notification-dropdown {
            position: absolute; top: calc(100% + 8px); right: 0;
            width: 310px; max-width: 92vw;
            background: var(--surface); border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1), 0 2px 6px rgba(0,0,0,0.06);
            border: 1px solid var(--border);
            overflow: hidden; z-index: 9999;
            animation: dropdownFade 0.15s ease-out;
        }

        .notification-item {
            padding: 11px 14px; border-bottom: 1px solid #F1F5F9;
            transition: background 0.12s; cursor: pointer;
        }
        .notification-item:hover { background: #F8FAFC; }
        .notification-item.unread { background: #F0FDFB; }

        .user-menu-dropdown {
            position: absolute; top: calc(100% + 8px); right: 0;
            width: 210px; background: var(--surface); border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1), 0 2px 6px rgba(0,0,0,0.06);
            border: 1px solid var(--border);
            overflow: hidden; z-index: 9999;
            animation: dropdownFade 0.15s ease-out;
        }

        .user-menu-item {
            padding: 8px 14px; display: flex; align-items: center; gap: 9px;
            color: var(--text-primary); transition: background 0.12s;
            cursor: pointer; text-decoration: none; font-size: 0.8125rem;
        }
        .user-menu-item:hover { background: #F8FAFC; }
        .user-menu-item i { width: 16px; color: var(--text-muted); font-size: 0.8125rem; }

        @keyframes dropdownFade {
            from { opacity: 0; transform: translateY(-4px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }

        /* ─── Status Dots ────────────────────────────────────────── */
        .status-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
        .status-pending   { background: var(--warning); }
        .status-approved  { background: var(--info); }
        .status-assigned  { background: #7C3AED; }
        .status-picked-up { background: #D97706; }
        .status-in_transit { background: #2563EB; }
        .status-delivered { background: var(--success); }
        .status-completed { background: #059669; }
        .status-cancelled { background: var(--danger); }

        /* ─── Animations ─────────────────────────────────────────── */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in { animation: slideIn 0.18s ease-out forwards; }

        /* ─── Misc ───────────────────────────────────────────────── */
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        [x-cloak] { display: none !important; }

        /* ─── Sidebar section divider ─────────────────────────────── */
        .sidebar-section-label {
            font-size: 0.625rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            padding: 8px 20px 4px;
            margin-top: 4px;
        }
        .collapsed .sidebar-section-label { display: none; }
    </style>

    @stack('styles')
</head>

<body>
    <div class="app-wrapper">

        <!-- ─── Desktop Sidebar ───────────────────────────────────── -->
        <aside class="sidebar-desktop" id="desktopSidebar">
            <div class="p-4 h-full flex flex-col">

                <!-- Logo -->
                <div class="mb-5 logo-section">
                    <div class="flex items-center space-x-2.5">
                        <div class="logo-wrapper w-9 h-9 flex items-center justify-center flex-shrink-0">
                            <img src="{{ asset('images/logo.png') }}"
                                alt="NeoProLab"
                                class="w-6 h-6 object-contain"
                                onerror="this.onerror=null;this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHJ4PSI4IiBmaWxsPSIjMDhCNEE5Ii8+PHBhdGggZD0iTTIwIDEwTDI1IDIwTDIwIDMwTDE1IDIwTDIwIDEwWiIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJ0cmFuc3BhcmVudCIvPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjQiIGZpbGw9IndoaXRlIi8+PC9zdmc+'">
                        </div>
                        <div class="logo-text">
                            <h1 class="font-semibold text-sm text-white tracking-tight leading-none">NeoProLab</h1>
                            <p class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.35)">Courier Management</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 space-y-0">
                    @yield('sidebar')
                </nav>

                <!-- User Profile -->
                <div class="mt-4 pt-3 border-t user-profile-section" style="border-color:rgba(255,255,255,0.08)">
                    <div class="flex items-center gap-2.5 px-2 py-2 rounded-lg" style="background:rgba(255,255,255,0.05)">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=0EA5A0&color=fff&bold=true&size=28"
                            alt="User" class="w-7 h-7 rounded-lg object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0 user-info">
                            <p class="font-medium text-xs text-white truncate leading-tight">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.4)">
                                @if(auth()->user()->isAdmin()) Admin
                                @elseif(auth()->user()->isCourier()) Courier
                                @else Client @endif
                            </p>
                        </div>
                        <div class="w-1.5 h-1.5 rounded-full status-dot" style="background:#34D399" class="status-dot"></div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ─── Mobile Sidebar ────────────────────────────────────── -->
        <div id="mobileSidebar" class="sidebar-mobile">
            <div class="p-4 h-full flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="logo-wrapper w-9 h-9 flex items-center justify-center flex-shrink-0">
                            <img src="{{ asset('images/logo.png') }}"
                                alt="NeoProLab" class="w-6 h-6 object-contain"
                                onerror="this.onerror=null;this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHJ4PSI4IiBmaWxsPSIjMDhCNEE5Ii8+PHBhdGggZD0iTTIwIDEwTDI1IDIwTDIwIDMwTDE1IDIwTDIwIDEwWiIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJ0cmFuc3BhcmVudCIvPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjQiIGZpbGw9IndoaXRlIi8+PC9zdmc+'">
                        </div>
                        <span class="font-semibold text-sm text-white">NeoProLab</span>
                    </div>
                    <button id="closeMobileSidebar" class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors" style="background:rgba(255,255,255,0.08)">
                        <i class="fas fa-times text-xs text-white opacity-70"></i>
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto space-y-0">
                    @yield('sidebar')
                </nav>
                <div class="mt-4 pt-3 border-t" style="border-color:rgba(255,255,255,0.08)">
                    <div class="flex items-center gap-2.5 px-2 py-2 rounded-lg" style="background:rgba(255,255,255,0.05)">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=0EA5A0&color=fff&bold=true&size=28"
                            alt="User" class="w-7 h-7 rounded-lg object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-xs text-white truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-[10px]" style="color:rgba(255,255,255,0.4)">
                                @if(auth()->user()->isAdmin()) Admin
                                @elseif(auth()->user()->isCourier()) Courier
                                @else Client @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="mobile-overlay"></div>

        <!-- ─── Main Content ──────────────────────────────────────── -->
        <div class="main-content">

            <!-- Navbar -->
            <header class="navbar">
                <div class="flex items-center justify-between px-4 h-full">
                    <div class="flex items-center gap-2">
                        <!-- Mobile menu -->
                        <button id="mobileMenuButton" class="md:hidden w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors flex items-center justify-center">
                            <i class="fas fa-bars text-sm"></i>
                        </button>
                        <!-- Desktop menu -->
                        <button id="desktopMenuButton" class="desktop-menu-btn w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors items-center justify-center">
                            <i class="fas fa-bars text-sm"></i>
                        </button>
                        <h1 class="text-sm font-semibold text-gray-800 truncate ml-1">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center gap-1.5 flex-shrink-0">

                        <!-- Notifications -->
                        <div class="dropdown-container"
                            x-data="{
                                open: false, notifications: [], unreadCount: 0, loading: false,
                                fetchNotifications() {
                                    this.loading = true;
                                    fetch('/notifications/recent', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                                    .then(r => r.json())
                                    .then(d => { this.notifications = d.notifications || []; this.unreadCount = d.unread_count || 0; })
                                    .catch(() => { this.notifications = []; this.unreadCount = 0; })
                                    .finally(() => { this.loading = false; });
                                },
                                markAsReadAndNavigate(id, url) {
                                    const t = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content');
                                    if (!t) { window.location.href = url; return; }
                                    fetch(`/notifications/${id}/read`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': t, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                                    .finally(() => { window.location.href = url; });
                                },
                                markAllAsRead() {
                                    const t = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content');
                                    if (!t) return;
                                    fetch('/notifications/read-all', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': t, 'Accept': 'application/json' } })
                                    .then(() => this.fetchNotifications()).catch(e => console.error(e));
                                }
                            }"
                            x-init="fetchNotifications(); setInterval(() => { if (open) fetchNotifications(); }, 30000)"
                            @click.away="open = false">

                            <button @click="open = !open" class="relative w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors flex items-center justify-center">
                                <i class="fas fa-bell text-sm"></i>
                                <span x-show="unreadCount > 0" x-text="unreadCount" class="notification-badge" x-cloak></span>
                            </button>

                            <div x-show="open" class="notification-dropdown" x-cloak @click.stop>
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                    <h3 class="font-semibold text-xs text-gray-800">Notifications</h3>
                                    <button x-show="unreadCount > 0" @click="markAllAsRead" class="text-[11px] text-teal-600 hover:text-teal-700 font-medium">
                                        Mark all read
                                    </button>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="loading">
                                        <div class="flex items-center justify-center py-8">
                                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                        </div>
                                    </template>
                                    <template x-if="!loading && (!notifications || notifications.length === 0)">
                                        <div class="text-center py-8 px-4">
                                            <i class="far fa-bell-slash text-2xl text-gray-300 mb-2"></i>
                                            <p class="text-gray-400 text-xs">No notifications</p>
                                        </div>
                                    </template>
                                    <template x-if="!loading && notifications && notifications.length > 0">
                                        <div>
                                            <template x-for="notification in notifications" :key="notification.id">
                                                <a :href="notification.url || '#'"
                                                   @click.prevent="markAsReadAndNavigate(notification.id, notification.url || '#')"
                                                   class="notification-item block"
                                                   :class="{ 'unread': !notification.read_at }">
                                                    <div class="flex items-start gap-3">
                                                        <i :class="notification.icon || 'fas fa-bell'"
                                                           :class="'text-' + (notification.color || 'teal') + '-500'"
                                                           class="mt-0.5 text-sm flex-shrink-0"></i>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-medium text-gray-800" x-text="notification.title || 'Notification'"></p>
                                                            <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-2" x-text="notification.message || ''"></p>
                                                            <p class="text-[10px] text-gray-400 mt-1" x-text="notification.created_at_human || ''"></p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                <div class="border-t border-gray-100 p-3 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-xs text-teal-600 hover:text-teal-700 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="dropdown-container" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center gap-1.5 p-1 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=0EA5A0&color=fff&bold=true&size=28"
                                    alt="User" class="w-7 h-7 rounded-lg object-cover">
                                <i class="fas fa-chevron-down text-[9px] text-gray-400 hidden md:inline"></i>
                            </button>
                            <div x-show="open" class="user-menu-dropdown" x-cloak @click.stop>
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="font-semibold text-xs text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.index') }}" class="user-menu-item">
                                    <i class="fas fa-user"></i>
                                    <span>My Profile</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('profile.edit') }}#password" class="user-menu-item">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                                @endif
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="user-menu-item" style="color:#DC2626">
                                        <i class="fas fa-sign-out-alt" style="color:#DC2626"></i>
                                        <span>Logout</span>
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="content-wrapper">
                <div class="content-container">
                    @hasSection('breadcrumbs')
                    <div class="mb-3 animate-slide-in">
                        <nav aria-label="Breadcrumb">
                            <ol class="inline-flex items-center flex-wrap gap-1">
                                <li class="inline-flex items-center">
                                    <a href="#" class="inline-flex items-center text-xs text-gray-400 hover:text-teal-600 transition-colors">
                                        <i class="fas fa-home mr-1 text-[10px]"></i>Home
                                    </a>
                                </li>
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    </div>
                    @endif

                    <div class="space-y-4">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton  = document.getElementById('mobileMenuButton');
            const desktopMenuButton = document.getElementById('desktopMenuButton');
            const mobileSidebar     = document.getElementById('mobileSidebar');
            const desktopSidebar    = document.getElementById('desktopSidebar');
            const mobileOverlay     = document.getElementById('mobileOverlay');
            const closeButton       = document.getElementById('closeMobileSidebar');

            if (localStorage.getItem('desktopSidebarCollapsed') === 'true' && window.innerWidth >= 768) {
                desktopSidebar.classList.add('collapsed');
            }

            if (desktopMenuButton) {
                desktopMenuButton.addEventListener('click', function() {
                    if (window.innerWidth >= 768) {
                        desktopSidebar.classList.toggle('collapsed');
                        localStorage.setItem('desktopSidebarCollapsed', desktopSidebar.classList.contains('collapsed'));
                    }
                });
            }

            function openMobileSidebar()  { mobileSidebar.classList.add('open');    mobileOverlay.classList.add('active');    document.body.style.overflow = 'hidden'; }
            function closeMobileSidebar() { mobileSidebar.classList.remove('open'); mobileOverlay.classList.remove('active'); document.body.style.overflow = ''; }

            if (mobileMenuButton) mobileMenuButton.addEventListener('click', openMobileSidebar);
            if (closeButton) closeButton.addEventListener('click', closeMobileSidebar);
            if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileSidebar);
            document.addEventListener('keydown', e => { if (e.key === 'Escape' && mobileSidebar.classList.contains('open')) closeMobileSidebar(); });
            window.addEventListener('resize', () => { if (window.innerWidth >= 768 && mobileSidebar.classList.contains('open')) closeMobileSidebar(); });
        });
    </script>

    @stack('scripts')
</body>
</html>