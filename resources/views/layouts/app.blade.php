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
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
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

        /* Scrollbar Styling */
        .sidebar-desktop:not(.collapsed)::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-desktop:not(.collapsed)::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-desktop:not(.collapsed)::-webkit-scrollbar-thumb {
            background: rgba(203, 213, 225, 0.3);
            border-radius: 4px;
        }

        .sidebar-desktop:not(.collapsed)::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }

        .sidebar-desktop.collapsed {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .sidebar-desktop.collapsed::-webkit-scrollbar {
            display: none;
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
            transition: width 0.2s ease;
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
            transition: transform 0.2s ease;
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
            background: #F8FAFC;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            flex-shrink: 0;
            position: relative;
            z-index: 45;
        }

        /* Content Area */
        .content-wrapper {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.25rem;
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

        @media (max-width: 767px) {
            .sidebar-desktop {
                display: none;
            }
        }

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
            padding: 10px 16px;
            border-radius: 8px;
            margin: 2px 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
            white-space: nowrap;
        }

        .collapsed .sidebar-item {
            padding: 10px;
            justify-content: center;
            gap: 0;
        }

        .collapsed .sidebar-item span {
            display: none;
        }

        .collapsed .sidebar-item i {
            margin: 0;
            font-size: 1.2rem;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .collapsed .sidebar-item:hover {
            transform: scale(1.05);
        }

        .sidebar-item.active {
            background: var(--teal);
            color: white;
        }

        .sidebar-item i {
            width: 20px;
            font-size: 1.1rem;
        }

        /* Logo Section */
        .logo-section {
            transition: all 0.2s ease;
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
            padding: 0.5rem;
        }

        .collapsed .user-profile-section .user-info {
            display: none;
        }

        .collapsed .user-profile-section .status-dot {
            display: none;
        }

        /* Desktop menu button */
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
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            height: 100%;
            width: 100%;
            border: 1px solid #EDF2F7;
        }

        @media (max-width: 640px) {
            .stat-card {
                padding: 1rem;
            }
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #EDF2F7;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th {
            background: #F8FAFC;
            font-weight: 600;
            color: #1E293B;
            padding: 12px 16px;
            border-bottom: 1px solid #E2E8F0;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid #F1F5F9;
            color: #475569;
            font-size: 0.875rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #F8FAFC;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            gap: 4px;
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
            top: -2px;
            right: -2px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid white;
        }

        /* Logo */
        .logo-wrapper {
            background: white;
            border-radius: 10px;
            padding: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.2s ease-out forwards;
        }

        /* Status Indicators */
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
        }

        .status-pending { background: var(--warning); }
        .status-approved { background: var(--success); }
        .status-assigned { background: var(--info); }
        .status-picked-up { background: #8B5CF6; }
        .status-delivered { background: var(--success); }
        .status-completed { background: #059669; }
        .status-cancelled { background: var(--danger); }

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

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            max-width: 90vw;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #EDF2F7;
            margin-top: 8px;
            overflow: hidden;
            z-index: 9999;
            transform-origin: top right;
            animation: dropdownFade 0.15s ease-out;
        }

        .notification-item {
            padding: 12px;
            border-bottom: 1px solid #F1F5F9;
            transition: all 0.15s;
            cursor: pointer;
        }

        .notification-item:hover {
            background: #F8FAFC;
        }

        .notification-item.unread {
            background: #F0FDF9;
        }

        .user-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 220px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #EDF2F7;
            margin-top: 8px;
            overflow: hidden;
            z-index: 9999;
            transform-origin: top right;
            animation: dropdownFade 0.15s ease-out;
        }

        .user-menu-item {
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1E293B;
            transition: all 0.15s;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .user-menu-item:hover {
            background: #F8FAFC;
        }

        .user-menu-item i {
            width: 18px;
            color: var(--gray);
            font-size: 0.9rem;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-5px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom scrollbar for content */
        .content-wrapper::-webkit-scrollbar {
            width: 4px;
        }

        .content-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        .content-wrapper::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }

        .content-wrapper::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="app-wrapper">
        <!-- Desktop Sidebar -->
        <aside class="sidebar-desktop" id="desktopSidebar">
            <div class="p-4 h-full flex flex-col">
                <!-- Logo Section -->
                <div class="mb-6 logo-section">
                    <div class="flex items-center space-x-3">
                        <div class="logo-wrapper w-10 h-10 flex items-center justify-center">
                            <img src="{{ asset('images/logo.png') }}"
                                alt="NeoPro Lab"
                                class="w-8 h-8 object-contain"
                                onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHJ4PSI4IiBmaWxsPSIjMDhCNEE5Ii8+PHBhdGggZD0iTTIwIDEwTDI1IDIwTDIwIDMwTDE1IDIwTDIwIDEwWiIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJ0cmFuc3BhcmVudCIvPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjQiIGZpbGw9IndoaXRlIi8+PC9zdmc+'">
                        </div>
                        <div class="logo-text">
                            <h1 class="font-bold text-base tracking-tight">NeoProLab</h1>
                            <p class="text-[10px] text-gray-400">Courier Management</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 space-y-0.5">
                    @yield('sidebar')
                </nav>

                <!-- User Profile Section -->
                <div class="mt-4 pt-4 border-t border-gray-700/30 user-profile-section">
                    <div class="flex items-center space-x-3 p-2 rounded-lg bg-white/5">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&bold=true&size=32"
                            alt="User"
                            class="w-8 h-8 rounded-lg object-cover">
                        <div class="flex-1 min-w-0 user-info">
                            <p class="font-medium text-sm truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-[10px] text-gray-400">
                                @if(auth()->user()->isAdmin())
                                <span class="flex items-center"><i class="fas fa-crown mr-1 text-[8px]"></i>Admin</span>
                                @elseif(auth()->user()->isCourier())
                                <span class="flex items-center"><i class="fas fa-motorcycle mr-1 text-[8px]"></i>Courier</span>
                                @else
                                <span class="flex items-center"><i class="fas fa-user mr-1 text-[8px]"></i>Client</span>
                                @endif
                            </p>
                        </div>
                        <div class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse status-dot"></div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar -->
        <div id="mobileSidebar" class="sidebar-mobile">
            <div class="p-4 h-full flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="logo-wrapper w-10 h-10 flex items-center justify-center">
                            <img src="{{ asset('images/logo.png') }}"
                                alt="NeoPro Lab"
                                class="w-8 h-8 object-contain"
                                onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHJ4PSI4IiBmaWxsPSIjMDhCNEE5Ii8+PHBhdGggZD0iTTIwIDEwTDI1IDIwTDIwIDMwTDE1IDIwTDIwIDEwWiIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJ0cmFuc3BhcmVudCIvPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjQiIGZpbGw9IndoaXRlIi8+PC9zdmc+'">
                        </div>
                        <span class="font-bold text-sm">NeoProLab</span>
                    </div>
                    <button id="closeMobileSidebar" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto space-y-0.5">
                    @yield('sidebar')
                </nav>

                <div class="mt-4 pt-4 border-t border-gray-700/30">
                    <div class="flex items-center space-x-3 p-2 rounded-lg bg-white/5">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&bold=true&size=32"
                            alt="User"
                            class="w-8 h-8 rounded-lg object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-[10px] text-gray-400">
                                @if(auth()->user()->isAdmin()) Admin
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
                <div class="flex items-center justify-between px-4 py-2">
                    <div class="flex items-center space-x-2">
                        <!-- Mobile menu button -->
                        <button id="mobileMenuButton" class="md:hidden w-8 h-8 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors flex items-center justify-center">
                            <i class="fas fa-bars text-sm"></i>
                        </button>
                        <!-- Desktop menu button -->
                        <button id="desktopMenuButton" class="desktop-menu-btn w-8 h-8 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors flex items-center justify-center">
                            <i class="fas fa-bars text-sm"></i>
                        </button>
                        <h1 class="text-base md:text-lg font-semibold text-gray-800 truncate">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-2 flex-shrink-0">
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
        markAsReadAndNavigate(id, url) {
            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content');
            if (!csrfToken) {
                window.location.href = url;
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
            .finally(() => {
                window.location.href = url;
            });
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
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(() => this.fetchNotifications())
                                    .catch(error => console.error('Error:', error));
                                }
                             }"
                             x-init="fetchNotifications(); setInterval(() => { if (open) fetchNotifications(); }, 30000)"
                             @click.away="open = false">

                            <button @click="open = !open" class="relative w-8 h-8 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors flex items-center justify-center">
                                <i class="fas fa-bell text-sm"></i>
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
                        <a :href="notification.url || '#'" 
                           @click.prevent="markAsReadAndNavigate(notification.id, notification.url || '#')"
                           class="notification-item block" 
                           :class="{ 'unread': !notification.read_at }">
                            <div class="flex items-start gap-3">
                                <i :class="notification.icon || 'fas fa-bell'" 
                                   :class="'text-' + (notification.color || 'teal') + '-500'" 
                                   class="mt-1 text-lg"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800" x-text="notification.title || 'Notification'"></p>
                                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="notification.message || ''"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="notification.created_at_human || ''"></p>
                                </div>
                            </div>
                        </a>
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
                            <button @click="open = !open" class="flex items-center space-x-1 focus:outline-none p-1 rounded-lg hover:bg-gray-100 transition-colors">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&bold=true&size=28"
                                    alt="User"
                                    class="w-7 h-7 rounded-lg object-cover">
                                <i class="fas fa-chevron-down text-[10px] text-gray-600 hidden md:inline"></i>
                            </button>

                            <div x-show="open" class="user-menu-dropdown" x-cloak @click.stop>
                                <div class="p-3 border-b border-gray-200 bg-gray-50">
                                    <p class="font-medium text-xs text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.index') }}" class="user-menu-item">
                                    <i class="fas fa-user"></i>
                                    <span class="text-xs">My Profile</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <!-- <a href="{{ route('admin.settings.index') }}" class="user-menu-item">
                                    <i class="fas fa-cog"></i>
                                    <span class="text-xs">Settings</span>
                                </a> -->
                                @endif

                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="user-menu-item text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span class="text-xs">Logout</span>
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
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center flex-wrap space-x-1">
                                <li class="inline-flex items-center">
                                    <a href="#" class="inline-flex items-center text-xs text-gray-600 hover:text-teal-600 transition-colors">
                                        <i class="fas fa-home mr-1"></i>
                                        Home
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

    <!-- Mobile Menu JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const desktopMenuButton = document.getElementById('desktopMenuButton');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const desktopSidebar = document.getElementById('desktopSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const closeButton = document.getElementById('closeMobileSidebar');

            // Check if sidebar state is saved
            const savedState = localStorage.getItem('desktopSidebarCollapsed');
            if (savedState === 'true' && window.innerWidth >= 768) {
                desktopSidebar.classList.add('collapsed');
            }

            // Desktop hamburger button
            if (desktopMenuButton) {
                desktopMenuButton.addEventListener('click', function() {
                    if (window.innerWidth >= 768) {
                        desktopSidebar.classList.toggle('collapsed');
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
                if (window.innerWidth >= 768 && mobileSidebar.classList.contains('open')) {
                    closeMobileSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>