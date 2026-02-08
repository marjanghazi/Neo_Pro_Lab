<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Courier Dashboard') - NeoProLab</title>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    
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
            z-index: 40;
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
            z-index: 30;
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

        .btn-secondary {
            background: white;
            color: var(--teal);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: 1px solid var(--teal);
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: var(--light-teal);
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

        .badge-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .status-draft { background: #9ca3af; }
        .status-pending_approval { background: #f59e0b; }
        .status-approved { background: #10b981; }
        .status-assigned { background: #3b82f6; }
        .status-accepted_by_courier { background: #8b5cf6; }
        .status-at_stop { background: #f59e0b; }
        .status-picked_up { background: #8b5cf6; }
        .status-in_transit { background: #0ea5e9; }
        .status-arrived_at_destination { background: #f59e0b; }
        .status-delivered { background: #10b981; }
        .status-completed { background: #059669; }
        .status-cancelled { background: #ef4444; }
        .status-rejected { background: #dc2626; }

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

        /* Courier Specific */
        .courier-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-online {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-offline {
            background: #f1f5f9;
            color: #64748b;
        }
        
        .status-busy {
            background: #fef3c7;
            color: #92400e;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-dot {
            position: absolute;
            left: -30px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid #e2e8f0;
            z-index: 1;
        }

        .timeline-dot.active {
            border-color: var(--teal);
            background: var(--teal);
            color: white;
        }

        .timeline-dot.completed {
            border-color: #10b981;
            background: #10b981;
            color: white;
        }

        /* Signature Canvas */
        .signature-container {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
            cursor: crosshair;
        }

        .signature-container.signing {
            border-color: var(--teal);
            background: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #e2e8f0;
            border-top-color: var(--teal);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Progress Bar */
        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--teal);
            transition: width 0.3s ease;
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .alert-info {
            background: #e0f2fe;
            color: #075985;
            border: 1px solid #7dd3fc;
        }
    </style>
    
    @stack('styles')
</head>
<body class="courier-layout">
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
                    <a href="{{ route('courier.dashboard') }}" class="sidebar-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('courier.assignments.index') }}" class="sidebar-item {{ request()->routeIs('courier.assignments.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>My Assignments</span>
                        @php
                            $pendingCount = auth()->user()->assignedRequests()->where('status', 'assigned')->count();
                        @endphp
                        @if($pendingCount > 0)
                        <span class="ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('courier.active-pickups') }}" class="sidebar-item {{ request()->routeIs('courier.active-pickups') ? 'active' : '' }}">
                        <i class="fas fa-box-open"></i>
                        <span>Active Pickups</span>
                        @php
                            $activePickups = auth()->user()->assignedRequests()->whereIn('status', ['accepted_by_courier', 'at_stop'])->count();
                        @endphp
                        @if($activePickups > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs rounded-full px-2 py-1">{{ $activePickups }}</span>
                        @endif
                    </a>

                    <a href="{{ route('courier.active-deliveries') }}" class="sidebar-item {{ request()->routeIs('courier.active-deliveries') ? 'active' : '' }}">
                        <i class="fas fa-truck-loading"></i>
                        <span>Active Deliveries</span>
                        @php
                            $activeDeliveries = auth()->user()->assignedRequests()->whereIn('status', ['picked_up', 'in_transit', 'arrived_at_destination'])->count();
                        @endphp
                        @if($activeDeliveries > 0)
                        <span class="ml-auto bg-purple-500 text-white text-xs rounded-full px-2 py-1">{{ $activeDeliveries }}</span>
                        @endif
                    </a>

                    <a href="{{ route('courier.history') }}" class="sidebar-item {{ request()->routeIs('courier.history') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        <span>Delivery History</span>
                    </a>

                    <div class="pt-4 mt-4 border-t border-gray-700">
                        <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Tools</p>
                        
                        <a href="#" id="toggle-tracking" class="sidebar-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Live Tracking</span>
                            <span id="tracking-status" class="ml-auto">
                                <span class="status-dot bg-green-500"></span>
                                <span class="text-xs">Active</span>
                            </span>
                        </a>
                        
                        <a href="{{ route('courier.proofs.index') }}" class="sidebar-item {{ request()->routeIs('courier.proofs.*') ? 'active' : '' }}">
                            <i class="fas fa-camera"></i>
                            <span>Proofs Gallery</span>
                        </a>

                        <a href="{{ route('courier.notifications') }}" class="sidebar-item {{ request()->routeIs('courier.notifications') ? 'active' : '' }}">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                            @php
                                $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                            @endphp
                            @if($unreadCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </div>

                    <div class="pt-4 mt-4 border-t border-gray-700">
                        <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Account</p>
                        
                        <a href="{{ route('courier.profile') }}" class="sidebar-item {{ request()->routeIs('courier.profile') ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    </div>
                </nav>
                
                <div class="mt-8 pt-8 border-t border-gray-700">
                    <div class="flex items-center space-x-3">
                        <img src="{{ auth()->user()->profile_photo ? Storage::url(auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . ' ' . auth()->user()->last_name) . '&background=0D8ABC&color=fff' }}" 
                             alt="User" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <div class="flex items-center">
                                <span id="courier-status" class="courier-status status-online mr-2">
                                    <span class="status-dot bg-green-500"></span>
                                    Online
                                </span>
                                <span class="text-xs text-gray-300">Courier</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar -->
        <div id="mobile-sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full transition-transform md:hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-700 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">N</span>
                        </div>
                        <span class="font-bold">NeoProLab</span>
                    </div>
                    <button onclick="closeMobileSidebar()" class="text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <nav class="space-y-1">
                    <a href="{{ route('courier.dashboard') }}" class="sidebar-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('courier.assignments.index') }}" class="sidebar-item {{ request()->routeIs('courier.assignments.*') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                        <i class="fas fa-tasks"></i>
                        <span>My Assignments</span>
                    </a>

                    <a href="{{ route('courier.active-pickups') }}" class="sidebar-item {{ request()->routeIs('courier.active-pickups') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                        <i class="fas fa-box-open"></i>
                        <span>Active Pickups</span>
                    </a>

                    <a href="{{ route('courier.active-deliveries') }}" class="sidebar-item {{ request()->routeIs('courier.active-deliveries') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                        <i class="fas fa-truck-loading"></i>
                        <span>Active Deliveries</span>
                    </a>

                    <a href="{{ route('courier.history') }}" class="sidebar-item {{ request()->routeIs('courier.history') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                        <i class="fas fa-history"></i>
                        <span>Delivery History</span>
                    </a>

                    <a href="{{ route('courier.proofs.index') }}" class="sidebar-item {{ request()->routeIs('courier.proofs.*') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                        <i class="fas fa-camera"></i>
                        <span>Proofs Gallery</span>
                    </a>

                    <a href="{{ route('courier.profile') }}" class="sidebar-item {{ request()->routeIs('courier.profile') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                </nav>
            </div>
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
                        <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Courier Dashboard')</h1>
                    </div>
                    
                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Location Status -->
                        <div id="location-status" class="hidden md:flex items-center space-x-2 px-3 py-2 bg-green-50 rounded-lg">
                            <span class="status-dot bg-green-500"></span>
                            <span class="text-sm text-green-700 font-medium">Tracking Active</span>
                        </div>
                        
                        <!-- Active Request Alert -->
                        <!-- <div id="active-request-alert" class="hidden">
                            <div class="flex items-center space-x-2 px-3 py-2 bg-blue-50 rounded-lg">
                                <i class="fas fa-truck text-blue-600"></i>
                                <span class="text-sm text-blue-700 font-medium">
                                    <span id="active-request-status"></span>
                                    <span id="active-request-number" class="font-bold"></span>
                                </span>
                                <a href="#" id="view-active-request" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div> -->
                        
                        <!-- Notifications -->
                        <div class="dropdown relative">
                            <button class="relative p-2 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-bell text-xl"></i>
                                @php
                                    $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                <span class="notification-badge">{{ $unreadCount }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="font-semibold">Notifications</h3>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                    <a href="#" class="dropdown-item {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                                        <div class="flex items-start">
                                            @switch($notification->type)
                                                @case('assignment_accepted')
                                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                                    @break
                                                @case('pickup_started')
                                                @case('pickup_completed')
                                                    <i class="fas fa-box text-blue-500 mt-1 mr-2"></i>
                                                    @break
                                                @case('in_transit')
                                                    <i class="fas fa-truck text-orange-500 mt-1 mr-2"></i>
                                                    @break
                                                @case('delivery_completed')
                                                    <i class="fas fa-check-double text-green-500 mt-1 mr-2"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-info-circle text-gray-500 mt-1 mr-2"></i>
                                            @endswitch
                                            <div>
                                                <p class="font-medium">{{ $notification->title }}</p>
                                                <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="p-4 text-center text-gray-500">
                                        <p>No notifications</p>
                                    </div>
                                    @endforelse
                                </div>
                                <div class="border-t border-gray-200">
                                    <a href="{{ route('courier.notifications') }}" class="dropdown-item text-center text-teal-600 font-medium">
                                        View All Notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="dropdown relative">
                            <button class="flex items-center space-x-3">
                                <img src="{{ auth()->user()->profile_photo ? Storage::url(auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . ' ' . auth()->user()->last_name) . '&background=0D8ABC&color=fff' }}" 
                                     alt="User" class="w-8 h-8 rounded-full">
                                <i class="fas fa-chevron-down text-gray-600"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="{{ route('courier.profile') }}" class="dropdown-item">
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
                                <a href="{{ route('courier.dashboard') }}" class="inline-flex items-center text-sm text-gray-700 hover:text-teal-600">
                                    <i class="fas fa-home mr-2"></i>
                                    Dashboard
                                </a>
                            </li>
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                </div>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success mb-6">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-error mb-6">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @if(session('warning'))
                <div class="alert alert-warning mb-6">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('warning') }}</span>
                </div>
                @endif

                @if(session('info'))
                <div class="alert alert-info mb-6">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('info') }}</span>
                </div>
                @endif

                <!-- Page Content -->
                <div class="space-y-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Modals -->
    <div id="photo-modal" class="modal">
        <div class="modal-content">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Upload Photo</h3>
                    <button onclick="closeModal('photo-modal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="photo-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="request_id" id="photo-request-id">
                    <input type="hidden" name="type" id="photo-type">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Photo</label>
                            <input type="file" name="photo" id="photo-input" accept="image/*" capture="environment" class="w-full" required>
                            <p class="text-xs text-gray-500 mt-1">Take a photo or select from gallery</p>
                        </div>
                        
                        <div id="photo-preview" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                            <img id="preview-image" class="w-full h-48 object-cover rounded-lg border">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                            <textarea name="notes" rows="3" class="w-full border rounded-lg p-2" placeholder="Add any notes..."></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal('photo-modal')" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-upload mr-2"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="signature-modal" class="modal">
        <div class="modal-content">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Capture Signature</h3>
                    <button onclick="closeModal('signature-modal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="signature-form">
                    @csrf
                    <input type="hidden" name="request_id" id="signature-request-id">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name</label>
                            <input type="text" name="recipient_name" class="w-full border rounded-lg p-2" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Relationship to Patient</label>
                            <input type="text" name="recipient_relationship" class="w-full border rounded-lg p-2" required placeholder="e.g., Nurse, Family Member, etc.">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Signature</label>
                            <div class="signature-container" id="signature-pad">
                                <canvas id="signature-canvas"></canvas>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="button" onclick="clearSignature()" class="text-sm text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-undo mr-1"></i>Clear
                                </button>
                            </div>
                            <input type="hidden" name="signature" id="signature-data">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Photo (Optional)</label>
                            <input type="file" name="delivery_photo" accept="image/*" capture="environment" class="w-full">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                            <textarea name="delivery_notes" rows="2" class="w-full border rounded-lg p-2" placeholder="Add any delivery notes..."></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal('signature-modal')" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check mr-2"></i>Submit Delivery
                        </button>
                    </div>
                </form>
            </div>
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
            closeMobileSidebar();
        });

        function closeMobileSidebar() {
            mobileSidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Photo preview
        document.getElementById('photo-input')?.addEventListener('change', function(e) {
            const preview = document.getElementById('photo-preview');
            const previewImage = document.getElementById('preview-image');
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Signature Pad
        let signaturePad = null;
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('signature-canvas');
            if (canvas) {
                canvas.width = canvas.offsetWidth;
                canvas.height = 200;
                
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });
                
                // Make signature pad responsive
                window.addEventListener('resize', function() {
                    const canvas = document.getElementById('signature-canvas');
                    const data = signaturePad.toData();
                    
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;
                    
                    signaturePad.clear();
                    signaturePad.fromData(data);
                });
            }
        });

        function clearSignature() {
            if (signaturePad) {
                signaturePad.clear();
            }
        }

        // Update courier location
        let locationUpdateInterval = null;
        let isTrackingActive = true;

        function updateCourierLocation(requestId = null) {
            if (!isTrackingActive) return;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const data = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                        speed: position.coords.speed || 0,
                        heading: position.coords.heading || 0,
                        altitude: position.coords.altitude || 0,
                        request_id: requestId
                    };
                    
                    // Send to server
                    fetch('{{ route("courier.location.update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Location updated:', data);
                        
                        // Also cache location for real-time access
                        fetch('/courier/api/cache-location', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                                accuracy: position.coords.accuracy
                            })
                        });
                    })
                    .catch(error => {
                        console.error('Error updating location:', error);
                    });
                }, function(error) {
                    console.error('Geolocation error:', error);
                    // Handle different error cases
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            showAlert('Location permission denied. Please enable location services.', 'error');
                            break;
                        case error.POSITION_UNAVAILABLE:
                            showAlert('Location information unavailable.', 'error');
                            break;
                        case error.TIMEOUT:
                            showAlert('Location request timed out.', 'error');
                            break;
                    }
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            }
        }

        // Start location updates
        function startLocationUpdates(requestId = null) {
            if (locationUpdateInterval) {
                clearInterval(locationUpdateInterval);
            }
            
            // Update immediately
            updateCourierLocation(requestId);
            
            // Then update every 30 seconds
            locationUpdateInterval = setInterval(() => {
                updateCourierLocation(requestId);
            }, 30000);
            
            // Update status display
            document.getElementById('location-status').classList.remove('hidden');
            document.getElementById('tracking-status').innerHTML = `
                <span class="status-dot bg-green-500"></span>
                <span class="text-xs">Active</span>
            `;
            isTrackingActive = true;
        }

        // Stop location updates
        function stopLocationUpdates() {
            if (locationUpdateInterval) {
                clearInterval(locationUpdateInterval);
                locationUpdateInterval = null;
            }
            
            // Update status display
            document.getElementById('location-status').classList.add('hidden');
            document.getElementById('tracking-status').innerHTML = `
                <span class="status-dot bg-gray-500"></span>
                <span class="text-xs">Inactive</span>
            `;
            isTrackingActive = false;
        }

        // Toggle location tracking
        document.getElementById('toggle-tracking')?.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (isTrackingActive) {
                fetch('{{ route("courier.location.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ active: false })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        stopLocationUpdates();
                        showAlert('Location tracking stopped.', 'info');
                    }
                });
            } else {
                fetch('{{ route("courier.location.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ active: true })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        startLocationUpdates();
                        showAlert('Location tracking started.', 'success');
                    }
                });
            }
        });

        // Check for active request
        function checkActiveRequest() {
            fetch('{{ route("courier.active-request") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.active) {
                        document.getElementById('active-request-alert').classList.remove('hidden');
                        document.getElementById('active-request-status').textContent = data.request.status.replace('_', ' ') + ': ';
                        document.getElementById('active-request-number').textContent = data.request.request_number;
                        document.getElementById('view-active-request').href = `/courier/requests/${data.request.id}`;
                        
                        // Start location updates for this request
                        startLocationUpdates(data.request.id);
                    } else {
                        document.getElementById('active-request-alert').classList.add('hidden');
                    }
                });
        }

        // Check location status on load
        function checkLocationStatus() {
            fetch('{{ route("courier.location.status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.tracking_active) {
                        startLocationUpdates();
                    } else {
                        stopLocationUpdates();
                    }
                });
        }

        // Show alert function
        function showAlert(message, type = 'info') {
            // Remove any existing alerts
            document.querySelectorAll('.custom-alert').forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} custom-alert`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            
            document.querySelector('main').prepend(alertDiv);
            
            // Remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check for active request
            checkActiveRequest();
            
            // Check location status
            checkLocationStatus();
            
            // Check active request every minute
            setInterval(checkActiveRequest, 60000);
            
            // Check location status every 2 minutes
            setInterval(checkLocationStatus, 120000);
        });

        // Handle photo form submission
        document.getElementById('photo-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const requestId = document.getElementById('photo-request-id').value;
            const type = document.getElementById('photo-type').value;
            
            let url = '';
            if (type === 'pickup') {
                url = `/courier/requests/${requestId}/pickup-proof`;
            } else if (type === 'delivery') {
                url = `/courier/requests/${requestId}/submit-delivery`;
            }
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Upload failed');
                    });
                }
            })
            .catch(error => {
                showAlert(error.message, 'error');
            });
        });

        // Handle signature form submission
        document.getElementById('signature-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (signaturePad.isEmpty()) {
                showAlert('Please provide a signature', 'error');
                return;
            }
            
            // Get signature data
            const signatureData = signaturePad.toDataURL();
            document.getElementById('signature-data').value = signatureData;
            
            const formData = new FormData(this);
            const requestId = document.getElementById('signature-request-id').value;
            
            fetch(`/courier/requests/${requestId}/submit-delivery`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Submission failed');
                    });
                }
            })
            .catch(error => {
                showAlert(error.message, 'error');
            });
        });

        // Open photo modal for specific request and type
        window.openPhotoModal = function(requestId, type) {
            document.getElementById('photo-request-id').value = requestId;
            document.getElementById('photo-type').value = type;
            openModal('photo-modal');
        }

        // Open signature modal for specific request
        window.openSignatureModal = function(requestId) {
            document.getElementById('signature-request-id').value = requestId;
            
            // Clear previous signature
            if (signaturePad) {
                signaturePad.clear();
            }
            
            openModal('signature-modal');
        }

        // Handle workflow actions
        window.handleWorkflowAction = function(action, requestId) {
            let url = '';
            let confirmMessage = '';
            
            switch(action) {
                case 'start-pickup':
                    url = `/courier/requests/${requestId}/start-pickup`;
                    confirmMessage = 'Are you sure you want to start the pickup process?';
                    break;
                case 'start-transit':
                    url = `/courier/requests/${requestId}/start-transit`;
                    confirmMessage = 'Are you sure you want to start transit to delivery location?';
                    break;
                case 'arrive-destination':
                    url = `/courier/requests/${requestId}/arrive-destination`;
                    confirmMessage = 'Have you arrived at the delivery location?';
                    break;
                case 'complete':
                    url = `/courier/requests/${requestId}/complete`;
                    confirmMessage = 'Are you sure you want to mark this request as completed?';
                    break;
            }
            
            if (confirm(confirmMessage)) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Action failed');
                        });
                    }
                })
                .catch(error => {
                    showAlert(error.message, 'error');
                });
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>