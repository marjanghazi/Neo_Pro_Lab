<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') - NeoProLab</title>

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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
    <script src="//unpkg.com/alpinejs" defer></script>

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
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
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
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            flex-shrink: 0;
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

        .sidebar-item.active {
            background: var(--teal);
            color: white;
            box-shadow: 0 8px 16px -4px rgba(0, 184, 169, 0.3);
        }

        .sidebar-item i {
            width: 24px;
            font-size: 1.2rem;
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
    </style>

    @stack('styles')
</head>

<body>
    <div class="app-wrapper">
        <!-- Desktop Sidebar -->
        <aside class="sidebar-desktop">
            <div class="p-6 h-full flex flex-col">
                <!-- Logo Section -->
                <div class="mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="logo-container">
                            <div class="logo-wrapper w-14 h-14 flex items-center justify-center">
                                <img src="{{ asset('images/logo.png') }}"
                                    alt="NeoPro Lab"
                                    class="w-12 h-12 object-contain"
                                    onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTYiIGhlaWdodD0iNTYiIHZpZXdCb3g9IjAgMCA1NiA1NiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNTYiIGhlaWdodD0iNTYiIHJ4PSIxNCIgZmlsbD0iIzAwQjhBOSIvPjxwYXRoIGQ9Ik0yOCAxNEwzNSAyOEwyOCA0MkwyMSAyOEwyOCAxNFoiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0idHJhbnNwYXJlbnQiLz48Y2lyY2xlIGN4PSIyOCIgY3k9IjI4IiByPSI2IiBmaWxsPSJ3aGl0ZSIvPjwvc3ZnPg=='">
                            </div>
                        </div>
                        <div>
                            <h1 class="font-bold text-xl tracking-tight">NeoProLab</h1>
                            <p class="text-xs text-gray-400 mt-0.5">Courier Management</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto space-y-1">
                    @yield('sidebar')
                </nav>

                <!-- User Profile Section -->
                <div class="mt-6 pt-6 border-t border-gray-700/30">
                    <div class="flex items-center space-x-3 p-3 rounded-xl bg-white/5">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&bold=true&size=40"
                            alt="User"
                            class="w-10 h-10 rounded-xl object-cover border-2 border-teal-400/30">
                        <div class="flex-1 min-w-0">
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
                        <div class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></div>
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
                        <button id="mobileMenuButton" class="md:hidden w-10 h-10 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors flex items-center justify-center">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <h1 class="text-lg md:text-2xl font-bold text-gray-800 tracking-tight truncate">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-2 md:space-x-3 flex-shrink-0">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false, notifications: [], unreadCount: 0 }" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)" @click.away="open = false">
                            <button @click="open = !open" class="relative w-9 h-9 md:w-10 md:h-10 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors flex items-center justify-center">
                                <i class="fas fa-bell text-base md:text-lg"></i>
                                <span x-show="unreadCount > 0" x-text="unreadCount" class="notification-badge" x-cloak></span>
                            </button>

                            <!-- Notifications dropdown content (keep as is from your original) -->
                        </div>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none p-1.5 md:p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&bold=true&size=32"
                                    alt="User"
                                    class="w-7 h-7 md:w-8 md:h-8 rounded-lg object-cover">
                                <i class="fas fa-chevron-down text-xs text-gray-600 hidden md:inline"></i>
                            </button>

                            <!-- User menu dropdown content (keep as is from your original) -->
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
            const mobileSidebar = document.getElementById('mobileSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const closeButton = document.getElementById('closeMobileSidebar');

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