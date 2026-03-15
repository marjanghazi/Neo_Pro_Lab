<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') - NeoProLab</title>

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Icons/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/Icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ApexCharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0"></script>

    <!-- Alpine.js -->
<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --navy: #0A1929;
            --navy-light: #1E2A3A;
            --teal: #00B8A9;
            --teal-dark: #008B7A;
            --teal-light: rgba(0, 184, 169, 0.1);
            --white: #FFFFFF;
            --gray: #64748B;
            --gray-light: #F1F5F9;
            --dark: #0F172A;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Scrollbar Styling - Only show when sidebar is expanded */
        .sidebar-desktop:not(.collapsed)::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .sidebar-desktop:not(.collapsed)::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-desktop:not(.collapsed)::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 8px;
        }

        .sidebar-desktop:not(.collapsed)::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        /* Hide scrollbar when collapsed */
        .sidebar-desktop.collapsed {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
        }

        .sidebar-desktop.collapsed::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        /* Layout */
        .app-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            position: relative;
        }

        /* Sidebar - Desktop */
        .sidebar-desktop {
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--navy) 0%, var(--dark) 100%);
            color: white;
            overflow-y: auto;
            flex-shrink: 0;
            position: relative;
            z-index: 30;
            transition: width 0.3s ease;
        }

        .sidebar-desktop.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* Sidebar - Mobile */
        .sidebar-mobile {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--navy) 0%, var(--dark) 100%);
            color: white;
            overflow-y: auto;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar-mobile.open {
            transform: translateX(0);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 40;
            display: none;
        }

        .mobile-overlay.active {
            display: block;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
            transition: margin-left 0.3s ease;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            flex-shrink: 0;
            position: relative;
            z-index: 45;
        }

        /* Content Area */
        .content-wrapper {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
        }

        @media (max-width: 640px) {
            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Content Container */
        .content-container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        /* Hide desktop sidebar on mobile */
        @media (max-width: 767px) {
            .sidebar-desktop {
                display: none;
            }
        }

        /* Hide mobile sidebar on desktop */
        @media (min-width: 768px) {
            .sidebar-mobile {
                display: none;
            }

            .mobile-overlay {
                display: none !important;
            }
        }

        /* Sidebar Items */
        .sidebar-item {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 20px;
            border-radius: 12px;
            margin: 4px 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            white-space: nowrap;
        }

        .collapsed .sidebar-item {
            padding: 12px;
            justify-content: center;
            gap: 0;
        }

        .collapsed .sidebar-item span {
            display: none;
        }

        .collapsed .sidebar-item i {
            margin: 0;
            font-size: 1.4rem;
        }

        .sidebar-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--teal), transparent);
            transition: width 0.3s;
            opacity: 0.2;
        }

        .sidebar-item:hover:before {
            width: 100%;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(4px);
        }

        .collapsed .sidebar-item:hover {
            transform: scale(1.1);
        }

        .sidebar-item.active {
            background: var(--teal);
            color: white;
            box-shadow: 0 8px 16px -4px rgba(0, 184, 169, 0.3);
        }

        .sidebar-item i {
            width: 24px;
            font-size: 1.2rem;
        }

        /* Logo Section Styles */
        .logo-section {
            transition: all 0.3s ease;
        }

        .collapsed .logo-section .logo-text {
            display: none;
        }

        .collapsed .logo-section .logo-wrapper {
            margin: 0 auto;
        }

        .collapsed .logo-section .flex {
            justify-content: center;
        }

        .collapsed .user-profile-section {
            justify-content: center;
            padding: 0.75rem;
        }

        .collapsed .user-profile-section .user-info {
            display: none;
        }

        .collapsed .user-profile-section img {
            margin: 0;
        }

        .collapsed .user-profile-section .status-dot {
            display: none;
        }

        /* Desktop hamburger button */
        .desktop-menu-btn {
            display: none;
        }

        @media (min-width: 768px) {
            .desktop-menu-btn {
                display: flex;
            }
        }

        /* Cards */
        .stat-card {
            background: linear-gradient(135deg, white 0%, #F8FAFC 100%);
            border-left: 4px solid var(--teal);
            border-radius: 16px;
            padding: 1.5rem;
            height: 100%;
            width: 100%;
        }

        @media (max-width: 640px) {
            .stat-card {
                padding: 1rem;
            }
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.6);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 600px;
        }

        th {
            background: #F8FAFC;
            font-weight: 600;
            color: var(--navy);
            padding: 16px 20px;
            border-bottom: 2px solid #E2E8F0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid #F1F5F9;
            color: #475569;
        }

        tr:hover td {
            background: #F8FAFC;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            gap: 6px;
            white-space: nowrap;
        }

        .badge-success {
            background: #DCFCE7;
            color: #166534;
        }

        .badge-warning {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-info {
            background: #E0F2FE;
            color: #075985;
        }

        .badge-primary {
            background: var(--teal-light);
            color: var(--teal-dark);
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: linear-gradient(135deg, var(--danger), #DC2626);
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid white;
        }

        /* Logo */
        .logo-container {
            background: linear-gradient(135deg, rgba(0, 184, 169, 0.1) 0%, rgba(0, 139, 122, 0.1) 100%);
            backdrop-filter: blur(4px);
            border-radius: 16px;
            padding: 2px;
        }

        .logo-wrapper {
            background: white;
            border-radius: 14px;
            padding: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out forwards;
        }

        /* Status Indicators */
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.5);
        }

        .status-pending {
            background: var(--warning);
        }

        .status-approved {
            background: var(--success);
        }

        .status-assigned {
            background: var(--info);
        }

        .status-picked-up {
            background: #8B5CF6;
        }

        .status-delivered {
            background: var(--success);
        }

        .status-completed {
            background: #059669;
        }

        .status-cancelled {
            background: var(--danger);
        }

        /* Line Clamp */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Dropdown Styles */
        .dropdown-container {
            position: relative;
        }

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 380px;
            max-width: 90vw;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(226, 232, 240, 0.6);
            margin-top: 12px;
            overflow: hidden;
            z-index: 9999;
            transform-origin: top right;
            animation: dropdownFade 0.2s ease-out;
        }

        .notification-item {
            padding: 16px;
            border-bottom: 1px solid #F1F5F9;
            transition: all 0.2s;
            cursor: pointer;
            background: white;
        }

        .notification-item:hover {
            background: #F8FAFC;
            transform: translateX(2px);
        }

        .notification-item.unread {
            background: #F0FDF9;
            position: relative;
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--teal);
            border-radius: 3px 0 0 3px;
        }

        /* User Menu Dropdown */
        .user-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 260px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(226, 232, 240, 0.6);
            margin-top: 12px;
            overflow: hidden;
            z-index: 9999;
            transform-origin: top right;
            animation: dropdownFade 0.2s ease-out;
        }

        .user-menu-item {
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1E293B;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            background: white;
        }

        .user-menu-item:hover {
            background: #F8FAFC;
            padding-left: 22px;
        }

        .user-menu-item i {
            width: 20px;
            color: var(--gray);
            font-size: 1rem;
        }

        .user-menu-item.text-red-600 i {
            color: var(--danger);
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="app-wrapper">
        <!-- Desktop Sidebar -->
        <aside class="sidebar-desktop" id="desktopSidebar">
            <div class="p-6 h-full flex flex-col">
                <!-- Logo Section -->
                <div class="mb-8 logo-section">
                    <div class="flex items-center space-x-3">
                        <div class="logo-container">
                            <div class="logo-wrapper w-14 h-14 flex items-center justify-center">
                                <img src="{{ asset('images/logo.png') }}"
                                    alt="NeoPro Lab"
                                    class="w-12 h-12 object-contain"
                                    onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTYiIGhlaWdodD0iNTYiIHZpZXdCb3g9IjAgMCA1NiA1NiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNTYiIGhlaWdodD0iNTYiIHJ4PSIxNCIgZmlsbD0iIzAwQjhBOSIvPjxwYXRoIGQ9Ik0yOCAxNEwzNSAyOEwyOCA0MkwyMSAyOEwyOCAxNFoiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0idHJhbnNwYXJlbnQiLz48Y2lyY2xlIGN4PSIyOCIgY3k9IjI4IiByPSI2IiBmaWxsPSJ3aGl0ZSIvPjwvc3ZnPg=='">
                            </div>
                        </div>
                        <div class="logo-text">
                            <h1 class="font-bold text-xl tracking-tight">NeoProLab</h1>
                            <p class="text-xs text-gray-400 mt-0.5">Courier Management</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 space-y-1">
                    @yield('sidebar')
                </nav>

                <!-- User Profile Section -->
                <div class="mt-6 pt-6 border-t border-gray-700/30 user-profile-section">
                    <div class="flex items-center space-x-3 p-3 rounded-xl bg-white/5">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&bold=true&size=40"
                            alt="User"
                            class="w-10 h-10 rounded-xl object-cover border-2 border-teal-400/30">
                        <div class="flex-1 min-w-0 user-info">
                            <p class="font-semibold text-sm truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-xs text-gray-400">
                                @if(auth()->user()->isAdmin())
                                <span class="flex items-center"><i class="fas fa-crown mr-1 text-xs"></i>Administrator</span>
                                @elseif(auth()->user()->isCourier())
                                <span class="flex items-center"><i class="fas fa-motorcycle mr-1 text-xs"></i>Courier</span>
                                @else
                                <span class="flex items-center"><i class="fas fa-user mr-1 text-xs"></i>Client</span>
                                @endif
                            </p>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-teal-400 animate-pulse status-dot"></div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar -->
        <div id="mobileSidebar" class="sidebar-mobile">
            <div class="p-6 h-full flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="logo-wrapper w-12 h-12 flex items-center justify-center">
                            <img src="{{ asset('images/logo.png') }}"
                                alt="NeoPro Lab"
                                class="w-10 h-10 object-contain"
                                onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHJ4PSIxMCIgZmlsbD0iIzAwQjhBOSIvPjxwYXRoIGQ9Ik0yMCAxMEwyNSAyMEwyMCAzMEwxNSAyMEwyMCAxMFoiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0idHJhbnNwYXJlbnQiLz48Y2lyY2xlIGN4PSIyMCIgY3k9IjIwIiByPSI0IiBmaWxsPSJ3aGl0ZSIvPjwvc3ZnPg=='">
                        </div>
                        <span class="font-bold text-lg">NeoProLab</span>
                    </div>
                    <button id="closeMobileSidebar" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto space-y-1">
                    @yield('sidebar')
                </nav>

                <div class="mt-6 pt-6 border-t border-gray-700/30">
                    <div class="flex items-center space-x-3 p-3 rounded-xl bg-white/5">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&bold=true&size=40"
                            alt="User"
                            class="w-10 h-10 rounded-xl object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-xs text-gray-400">
                                @if(auth()->user()->isAdmin()) Administrator
                                @elseif(auth()->user()->isCourier()) Courier
                                @else Client
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="mobile-overlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navbar -->
            <header class="navbar">
                <div class="flex items-center justify-between px-4 md:px-6 py-3 md:py-4">
                    <div class="flex items-center space-x-3">
                        <!-- Mobile menu button -->
                        <button id="mobileMenuButton" class="md:hidden w-10 h-10 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors flex items-center justify-center">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <!-- Desktop menu button (hamburger) -->
                        <button id="desktopMenuButton" class="desktop-menu-btn w-10 h-10 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors flex items-center justify-center">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <h1 class="text-lg md:text-2xl font-bold text-gray-800 tracking-tight truncate">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-2 md:space-x-3 flex-shrink-0">
                        <!-- Notifications -->
<div class="dropdown-container" 
     x-data="{
        open: false,
        notifications: [],
        unreadCount: 0,
        loading: false,
        fetchNotifications() {
            this.loading = true;
            fetch('/notifications/recent', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                // Don't show error to user, just set empty state
                this.notifications = [];
                this.unreadCount = 0;
            })
            .finally(() => {
                this.loading = false;
            });
        },
        markAsRead(id) {
            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content');
            if (!csrfToken) {
                console.error('CSRF token not found');
                return;
            }

            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(() => {
                this.fetchNotifications();
            })
            .catch(error => console.error('Error marking notification as read:', error));
        },
        markAllAsRead() {
            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content');
            if (!csrfToken) {
                console.error('CSRF token not found');
                return;
            }

            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(() => {
                this.fetchNotifications();
            })
            .catch(error => console.error('Error marking all notifications as read:', error));
        }
     }"
     x-init="fetchNotifications(); setInterval(() => { if (open) fetchNotifications(); }, 30000)"
     @click.away="open = false">

    <button @click="open = !open" class="relative w-9 h-9 md:w-10 md:h-10 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors flex items-center justify-center">
        <i class="fas fa-bell text-base md:text-lg"></i>
        <span x-show="unreadCount > 0" x-text="unreadCount" class="notification-badge" x-cloak></span>
    </button>

    <!-- Notifications dropdown -->
    <div x-show="open" class="notification-dropdown" x-cloak @click.stop>
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-800">Notifications</h3>
            <button x-show="unreadCount > 0" @click="markAllAsRead" class="text-xs text-teal-600 hover:text-teal-700 font-medium transition-colors">
                Mark all as read
            </button>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            <!-- Loading State -->
            <template x-if="loading">
                <div class="flex items-center justify-center py-8">
                    <i class="fas fa-spinner fa-spin text-teal-500 text-xl"></i>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && (!notifications || notifications.length === 0)">
                <div class="text-center py-8 px-4">
                    <i class="far fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-sm">No notifications</p>
                </div>
            </template>

            <!-- Notifications List -->
            <template x-if="!loading && notifications && notifications.length > 0">
                <div>
                    <template x-for="notification in notifications" :key="notification.id">
                        <div @click="markAsRead(notification.id)" class="notification-item" :class="{ 'unread': !notification.read_at }">
                            <div class="flex items-start gap-3">
                                <i :class="notification.icon || 'fas fa-bell'" 
                                   :class="'text-' + (notification.color || 'teal') + '-500'" 
                                   class="mt-1 text-lg"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800" x-text="notification.title || 'Notification'"></p>
                                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="notification.message || ''"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="notification.created_at_human || ''"></p>
                                </div>
                                <span x-show="!notification.read_at" class="w-2 h-2 bg-teal-500 rounded-full flex-shrink-0 mt-2"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Footer Link -->
        <div class="border-t border-gray-200 p-3 text-center bg-gray-50">
            <a href="{{ route('notifications.index') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium transition-colors">
                View all notifications
            </a>
        </div>
    </div>
</div>

                        <!-- User Menu -->
                        <div class="dropdown-container" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none p-1.5 md:p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&bold=true&size=32"
                                    alt="User"
                                    class="w-7 h-7 md:w-8 md:h-8 rounded-lg object-cover">
                                <i class="fas fa-chevron-down text-xs text-gray-600 hidden md:inline"></i>
                            </button>

                            <!-- User menu dropdown -->
                            <div x-show="open" class="user-menu-dropdown" x-cloak @click.stop>
                                <div class="p-4 border-b border-gray-200 bg-gray-50">
                                    <p class="font-semibold text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                                    <p class="text-xs text-gray-500 mt-1 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.index') }}" class="user-menu-item">
                                    <i class="fas fa-user"></i>
                                    <span>My Profile</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <!-- <a href="{{ route('admin.settings.index') }}" class="user-menu-item">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a> -->
                                @endif

                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="user-menu-item text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt"></i>
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
                    <div class="mb-4 md:mb-6 animate-slide-in">
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center flex-wrap space-x-1 md:space-x-2">
                                <li class="inline-flex items-center">
                                    <a href="#" class="inline-flex items-center text-xs md:text-sm text-gray-600 hover:text-teal-600 transition-colors">
                                        <i class="fas fa-home mr-1.5"></i>
                                        Home
                                    </a>
                                </li>
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    </div>
                    @endif

                    <div class="space-y-4 md:space-y-6">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Menu JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const desktopMenuButton = document.getElementById('desktopMenuButton');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const desktopSidebar = document.getElementById('desktopSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const closeButton = document.getElementById('closeMobileSidebar');

            // Check if sidebar state is saved in localStorage
            const savedState = localStorage.getItem('desktopSidebarCollapsed');
            if (savedState === 'true' && window.innerWidth >= 768) {
                desktopSidebar.classList.add('collapsed');
            }

            // Desktop hamburger button functionality
            if (desktopMenuButton) {
                desktopMenuButton.addEventListener('click', function() {
                    if (window.innerWidth >= 768) {
                        desktopSidebar.classList.toggle('collapsed');
                        // Save state to localStorage
                        localStorage.setItem('desktopSidebarCollapsed', desktopSidebar.classList.contains('collapsed'));
                    }
                });
            }

            // Mobile sidebar functions
            function openMobileSidebar() {
                mobileSidebar.classList.add('open');
                mobileOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileSidebar() {
                mobileSidebar.classList.remove('open');
                mobileOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', openMobileSidebar);
            }

            if (closeButton) {
                closeButton.addEventListener('click', closeMobileSidebar);
            }

            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', closeMobileSidebar);
            }

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileSidebar.classList.contains('open')) {
                    closeMobileSidebar();
                }
            });

            // Handle resize
            window.addEventListener('resize', function() {
                // Close mobile sidebar when resizing to desktop
                if (window.innerWidth >= 768 && mobileSidebar.classList.contains('open')) {
                    closeMobileSidebar();
                }

                // Reset desktop sidebar if needed
                if (window.innerWidth < 768 && desktopSidebar.classList.contains('collapsed')) {
                    // We keep the collapsed state but it's hidden on mobile anyway
                    // This is fine as it will be hidden
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>