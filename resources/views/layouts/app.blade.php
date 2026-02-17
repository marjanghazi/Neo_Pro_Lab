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
    <script src="//unpkg.com/alpinejs" defer></script>
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

        .status-pending {
            background: #f59e0b;
        }

        .status-approved {
            background: #10b981;
        }

        .status-assigned {
            background: #3b82f6;
        }

        .status-picked-up {
            background: #8b5cf6;
        }

        .status-delivered {
            background: #10b981;
        }

        .status-completed {
            background: #059669;
        }

        .status-cancelled {
            background: #ef4444;
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
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 flex-shrink-0 hidden md:block">
            <div class="p-6">
                <!-- Updated: Larger logo for desktop -->
                <div class="flex items-center space-x-4 mb-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center">
                        <div class="h-14 w-14 flex items-center justify-center">
                            <img src="{{ asset('images/logo.svg') }}" alt="NeoPro Lab Logo" class="h-full w-full object-contain" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHJ4PSIxMiIgZmlsbD0iIzI1NjNlYSIvPjxwYXRoIGQ9Ik0zMiAxNkw0MCAzMkwzMiA0OEwyNCAzMkwzMiAxNloiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0idHJhbnNwYXJlbnQiLz48Y2lyY2xlIGN4PSIzMiIgY3k9IjMyIiByPSI2IiBmaWxsPSJ3aGl0ZSIvPjwvc3ZnPg=='">
                        </div>
                    </div>
                    <div>
                        <span class="font-bold text-xl">NeoProLab</span>
                        <span class="text-xs text-gray-300 block">Courier System</span>
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
            <div class="p-6">
                <!-- Updated: Larger logo for mobile -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-700 rounded-lg flex items-center justify-center">
                            <div class="h-10 w-10 flex items-center justify-center">
                                <img src="{{ asset('images/logo.svg') }}" alt="NeoPro Lab Logo" class="h-full w-full object-contain" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHJ4PSIxMiIgZmlsbD0iIzI1NjNlYSIvPjxwYXRoIGQ9Ik0zMiAxNkw0MCAzMkwzMiA0OEwyNCAzMkwzMiAxNloiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0idHJhbnNwYXJlbnQiLz48Y2lyY2xlIGN4PSIzMiIgY3k9IjMyIiByPSI2IiBmaWxsPSJ3aGl0ZSIvPjwvc3ZnPg=='">
                            </div>
                        </div>
                        <span class="font-bold text-lg">NeoProLab</span>
                    </div>
                    <button onclick="closeMobileSidebar()" class="text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <nav class="space-y-1">
                    @yield('sidebar')
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
                        <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ 
                                open: false, 
                                notifications: [], 
                                unreadCount: 0,
                                init() {
                                    this.fetchNotifications();
                                    // Refresh notifications every 30 seconds
                                    setInterval(() => this.fetchNotifications(), 30000);
                                },
                                fetchNotifications() {
                                    fetch('{{ route("admin.notifications.recent") }}')
                                        .then(response => response.json())
                                        .then(data => {
                                            this.notifications = data.notifications;
                                            this.unreadCount = data.unread_count;
                                        })
                                        .catch(error => console.error('Error fetching notifications:', error));
                                },
                                markAsRead(notificationId) {
                                    fetch(`{{ url("admin/notifications") }}/${notificationId}/read`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            this.fetchNotifications();
                                        }
                                    })
                                    .catch(error => console.error('Error marking notification as read:', error));
                                },
                                markAllAsRead() {
                                    fetch('{{ route("admin.notifications.read-all") }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            this.fetchNotifications();
                                        }
                                    })
                                    .catch(error => console.error('Error marking all notifications as read:', error));
                                },
                                getNotificationUrl(notification) {
                                    if (notification.request_id) {
                                        return `{{ url("admin/requests") }}/${notification.request_id}`;
                                    }
                                    return `{{ url("admin/notifications") }}/${notification.id}`;
                                },
                                getNotificationIcon(type) {
                                    const icons = {
                                        'request_assigned': 'fas fa-user-check',
                                        'request_assigned_with_quote': 'fas fa-file-invoice-dollar',
                                        'quote_received': 'fas fa-tag',
                                        'status_update': 'fas fa-exchange-alt',
                                        'request_created': 'fas fa-plus-circle',
                                        'payment_received': 'fas fa-credit-card',
                                        'courier_online': 'fas fa-circle',
                                        'courier_offline': 'fas fa-circle',
                                        'system_alert': 'fas fa-exclamation-triangle',
                                        'price_calculated': 'fas fa-calculator',
                                        'quote_created': 'fas fa-file-invoice'
                                    };
                                    return icons[notification.type] || 'fas fa-bell';
                                },
                                getIconBackground(type) {
                                    const backgrounds = {
                                        'request_assigned': 'bg-blue-100 text-blue-600',
                                        'request_assigned_with_quote': 'bg-purple-100 text-purple-600',
                                        'quote_received': 'bg-green-100 text-green-600',
                                        'status_update': 'bg-yellow-100 text-yellow-600',
                                        'request_created': 'bg-indigo-100 text-indigo-600',
                                        'payment_received': 'bg-emerald-100 text-emerald-600',
                                        'courier_online': 'bg-green-100 text-green-600',
                                        'courier_offline': 'bg-gray-100 text-gray-600',
                                        'system_alert': 'bg-red-100 text-red-600',
                                        'price_calculated': 'bg-orange-100 text-orange-600',
                                        'quote_created': 'bg-teal-100 text-teal-600'
                                    };
                                    return backgrounds[type] || 'bg-gray-100 text-gray-600';
                                },
                                timeAgo(timestamp) {
                                    const date = new Date(timestamp);
                                    const now = new Date();
                                    const seconds = Math.floor((now - date) / 1000);
                                    
                                    const intervals = {
                                        year: 31536000,
                                        month: 2592000,
                                        week: 604800,
                                        day: 86400,
                                        hour: 3600,
                                        minute: 60,
                                        second: 1
                                    };
                                    
                                    for (let [unit, secondsInUnit] of Object.entries(intervals)) {
                                        const interval = Math.floor(seconds / secondsInUnit);
                                        if (interval >= 1) {
                                            return interval + ' ' + unit + (interval === 1 ? '' : 's') + ' ago';
                                        }
                                    }
                                    
                                    return 'just now';
                                }
                            }">
                            <button @click="open = !open" @click.away="open = false" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-bell text-xl"></i>
                                <span x-show="unreadCount > 0"
                                    x-text="unreadCount"
                                    class="notification-badge"
                                    :class="{ 'hidden': unreadCount === 0 }"></span>
                            </button>

                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                                style="display: none;">

                                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="font-semibold text-gray-700">Notifications</h3>
                                    <div class="flex space-x-2">
                                        <template x-if="unreadCount > 0">
                                            <button @click="markAllAsRead(); $event.preventDefault()" class="text-xs text-teal-600 hover:text-teal-800">
                                                Mark all read
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div class="max-h-96 overflow-y-auto">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <a :href="getNotificationUrl(notification)"
                                            @click="markAsRead(notification.id); open = false"
                                            class="block px-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition"
                                            :class="{ 'bg-teal-50': !notification.is_read }">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                        :class="getIconBackground(notification.type)">
                                                        <i :class="getNotificationIcon(notification.type)" class="text-xs"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                                    <p class="text-xs text-gray-600 mt-1 line-clamp-2" x-text="notification.message"></p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <p class="text-xs text-gray-400" x-text="timeAgo(notification.created_at)"></p>
                                                        <span x-show="!notification.is_read" class="w-2 h-2 bg-teal-600 rounded-full"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </template>

                                    <div x-show="notifications.length === 0" class="p-8 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                                            <i class="fas fa-bell-slash text-gray-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">No notifications</p>
                                    </div>
                                </div>

                                <div class="p-3 border-t border-gray-200 text-center">
                                    <a href="{{ route('admin.notifications.index') }}"
                                        @click="open = false"
                                        class="text-sm text-teal-600 hover:text-teal-800 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-3 focus:outline-none">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=0D8ABC&color=fff"
                                    alt="User" class="w-8 h-8 rounded-full">
                                <i class="fas fa-chevron-down text-gray-600"></i>
                            </button>
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                                style="display: none;">
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                @elseif(auth()->user()->isCourier())
                                <a href="{{ route('courier.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                @else
                                <a href="{{ route('client.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                @endif
                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
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
            closeMobileSidebar();
        });

        function closeMobileSidebar() {
            mobileSidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        }
    </script>

    @stack('scripts')
</body>

</html>