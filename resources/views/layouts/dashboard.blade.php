<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - NeoProLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0"></script>
    <style>
        :root {
            --navy: #0D1B2A;
            --teal: #00A9A5;
            --white: #FFFFFF;
            --gray: #7A7F85;
            --light-gray: #F5F7FA;
            --dark-navy: #0A1521;
            --light-teal: rgba(0, 169, 165, 0.1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--navy) 0%, var(--dark-navy) 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .sidebar-item {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 16px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-item.active {
            background: var(--teal);
            color: white;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--white) 0%, #f8fafc 100%);
            border-left: 4px solid var(--teal);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 169, 165, 0.3);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        table {
            min-width: 100%;
        }

        th {
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
            padding: 12px 16px;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:hover {
            background: #f8fafc;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #e0f2fe;
            color: #075985;
        }

        .badge-primary {
            background: var(--light-teal);
            color: var(--teal);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .status-pending { background: #f59e0b; }
        .status-approved { background: #10b981; }
        .status-assigned { background: #3b82f6; }
        .status-picked-up { background: #8b5cf6; }
        .status-delivered { background: #10b981; }
        .status-completed { background: #059669; }
        .status-cancelled { background: #ef4444; }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            z-index: 50;
            display: none;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            padding: 10px 16px;
            color: #475569;
            text-decoration: none;
            display: block;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: #f8fafc;
            color: var(--teal);
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 flex-shrink-0 hidden md:block">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-700 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">N</span>
                    </div>
                    <div>
                        <span class="font-bold text-lg">NeoProLab</span>
                        <span class="text-xs text-gray-300 block -mt-1">Courier System</span>
                    </div>
                </div>
                
                <nav class="space-y-1">
                    @yield('sidebar')
                </nav>
                
                <div class="mt-8 pt-8 border-t border-gray-700">
                    <div class="flex items-center space-x-3">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=0D8ABC&color=fff" 
                             alt="User" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-xs text-gray-300">
                                @if(auth()->user()->isAdmin())
                                    Administrator
                                @elseif(auth()->user()->isCourier())
                                    Courier
                                @else
                                    Client
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar -->
        <div id="mobile-sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full transition-transform md:hidden">
            <!-- Same content as desktop sidebar -->
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            <header class="navbar">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Left side -->
                    <div class="flex items-center space-x-4">
                        <button id="mobile-menu-button" class="md:hidden text-gray-700">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    
                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="dropdown relative">
                            <button class="relative p-2 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="notification-badge">3</span>
                            </button>
                            <div class="dropdown-menu">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="font-semibold">Notifications</h3>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <a href="#" class="dropdown-item">
                                        <p class="font-medium">New request assigned</p>
                                        <p class="text-sm text-gray-500">2 min ago</p>
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <p class="font-medium">Pickup completed</p>
                                        <p class="text-sm text-gray-500">1 hour ago</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="dropdown relative">
                            <button class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=0D8ABC&color=fff" 
                                     alt="User" class="w-8 h-8 rounded-full">
                                <i class="fas fa-chevron-down text-gray-600"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="{{ route('admin.profile') ?? route('client.profile') ?? route('courier.profile') }}" class="dropdown-item">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}" class="dropdown-item">
                                    @csrf
                                    <button type="submit" class="flex items-center text-red-600">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
                <!-- Breadcrumbs -->
                @hasSection('breadcrumbs')
                <div class="mb-6">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="#" class="inline-flex items-center text-sm text-gray-700 hover:text-teal-600">
                                    <i class="fas fa-home mr-2"></i>
                                    Home
                                </a>
                            </li>
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                </div>
                @endif

                <!-- Page Content -->
                <div class="space-y-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');

        mobileMenuButton.addEventListener('click', () => {
            mobileSidebar.classList.toggle('-translate-x-full');
            mobileOverlay.classList.toggle('hidden');
        });

        mobileOverlay.addEventListener('click', () => {
            mobileSidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>