@extends('layouts.' . (auth()->user()->isAdmin() ? 'admin' : (auth()->user()->isCourier() ? 'courier' : 'client')))

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Profile</span>
    </div>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Profile Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Profile Header -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        @if(auth()->user()->profile_image)
                        <img src="{{ Storage::url(auth()->user()->profile_image) }}"
                            alt="{{ auth()->user()->full_name }}"
                            class="w-20 h-20 rounded-full border-4 border-white shadow-lg">
                        @else
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&size=128"
                            alt="{{ auth()->user()->full_name }}"
                            class="w-20 h-20 rounded-full border-4 border-white shadow-lg">
                        @endif
                        <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ auth()->user()->full_name }}</h2>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if(auth()->user()->isAdmin()) bg-blue-100 text-blue-800
                                @elseif(auth()->user()->isCourier()) bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800
                                @endif">
                                @if(auth()->user()->isAdmin())
                                <i class="fas fa-crown mr-1"></i>Administrator
                                @elseif(auth()->user()->isCourier())
                                <i class="fas fa-motorcycle mr-1"></i>Courier
                                @else
                                <i class="fas fa-user mr-1"></i>Client
                                @endif
                            </span>
                            <span class="text-sm text-gray-500">ID: {{ auth()->user()->id }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}"
                    class="mt-4 md:mt-0 px-6 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 transition-all transform hover:scale-105 inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit Profile
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @if(auth()->user()->isAdmin())
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-700">Facilities</p>
                    <p class="text-2xl font-bold text-blue-900">{{ \App\Models\Facility::count() }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-700">Couriers</p>
                    <p class="text-2xl font-bold text-green-900">{{ \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'courier'))->count() }}</p>
                </div>
                @endif

                @if(auth()->user()->isCourier())
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-purple-700">Active Deliveries</p>
                    <p class="text-2xl font-bold text-purple-900">
                        {{ auth()->user()->assignedRequests()->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit'])->count() }}
                    </p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg">
                    <p class="text-sm text-orange-700">Completed</p>
                    <p class="text-2xl font-bold text-orange-900">
                        {{ auth()->user()->assignedRequests()->where('status', 'completed')->count() }}
                    </p>
                </div>
                @endif

                @if(auth()->user()->isClient())
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-purple-700">Total Requests</p>
                    <p class="text-2xl font-bold text-purple-900">
                        {{ auth()->user()->createdRequests()->count() }}
                    </p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-700">Completed</p>
                    <p class="text-2xl font-bold text-green-900">
                        {{ auth()->user()->createdRequests()->where('status', 'completed')->count() }}
                    </p>
                </div>
                @endif

                <div class="bg-teal-50 p-4 rounded-lg">
                    <p class="text-sm text-teal-700">Member Since</p>
                    <p class="text-2xl font-bold text-teal-900">{{ auth()->user()->created_at->format('M Y') }}</p>
                </div>
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <p class="text-sm text-indigo-700">Last Login</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Today' }}</p>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-user-circle text-teal-600 mr-2"></i>
                        Personal Information
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 w-32">Full Name:</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->full_name }}</span>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 w-32">Email:</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 w-32">Phone:</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->phone ?? 'Not set' }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-shield-alt text-teal-600 mr-2"></i>
                        Account Information
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 w-32">Role:</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->role->name }}</span>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 w-32">Status:</span>
                            <span class="font-medium {{ auth()->user()->is_active ? 'text-green-600' : 'text-red-600' }}">
                                <span class="w-2 h-2 rounded-full {{ auth()->user()->is_active ? 'bg-green-500' : 'bg-red-500' }} inline-block mr-2"></span>
                                {{ auth()->user()->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 w-32">Email Verified:</span>
                            <span class="font-medium {{ auth()->user()->email_verified_at ? 'text-green-600' : 'text-yellow-600' }}">
                                <span class="w-2 h-2 rounded-full {{ auth()->user()->email_verified_at ? 'bg-green-500' : 'bg-yellow-500' }} inline-block mr-2"></span>
                                {{ auth()->user()->email_verified_at ? 'Verified' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isCourier() && auth()->user()->vehicle_type)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-truck text-teal-600 mr-2"></i>
                    Vehicle Information
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 w-32">Vehicle Type:</span>
                        <span class="font-medium text-gray-800">{{ ucfirst(auth()->user()->vehicle_type) }}</span>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 w-32">Vehicle Number:</span>
                        <span class="font-medium text-gray-800">{{ auth()->user()->vehicle_number ?? 'Not set' }}</span>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->isClient())
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-building text-teal-600 mr-2"></i>
                    Facility Information
                </h4>
                @php
                $facility = auth()->user()->facilities()->first();
                @endphp
                @if($facility)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 w-32">Facility Name:</span>
                        <span class="font-medium text-gray-800">{{ $facility->name }}</span>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600 w-32">Status:</span>
                        <span class="font-medium {{ $facility->is_approved ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $facility->is_approved ? 'Approved' : 'Pending Approval' }}
                        </span>
                    </div>
                </div>
                @else
                <div class="p-3 bg-yellow-50 rounded-lg text-yellow-700">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    No facility assigned yet.
                </div>
                @endif
            </div>
            @endif
        </div>

       
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Account Status -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-shield-alt text-teal-600 mr-2"></i>
                Account Status
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="text-gray-700">Account Verified</span>
                    </div>
                    <span class="text-green-600 font-medium">
                        <i class="fas fa-check"></i>
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                        <span class="text-gray-700">Two-Factor Auth</span>
                    </div>
                    <span class="text-gray-600 text-sm">Disabled</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>
                        <span class="text-gray-700">Member Since</span>
                    </div>
                    <span class="font-medium text-gray-800">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-lock text-teal-600 mr-2"></i>
                Security
            </h3>
            <div class="space-y-2">
                <a href="{{ route('profile.edit') }}#password"
                    class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <i class="fas fa-key text-gray-600 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Change Password</span>
                    <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-teal-600"></i>
                </a>
                <a href="#" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <i class="fas fa-shield-alt text-gray-600 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Two-Factor Authentication</span>
                    <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-teal-600"></i>
                </a>
                <a href="#" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <i class="fas fa-history text-gray-600 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Login History</span>
                    <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-teal-600"></i>
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-link text-teal-600 mr-2"></i>
                Quick Links
            </h3>
            <div class="space-y-2">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
                    <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-blue-600">Dashboard</span>
                    <i class="fas fa-arrow-right ml-auto text-blue-600"></i>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition group">
                    <i class="fas fa-chart-bar text-green-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-green-600">Reports</span>
                    <i class="fas fa-arrow-right ml-auto text-green-600"></i>
                </a>
                @elseif(auth()->user()->isCourier())
                <a href="{{ route('courier.dashboard') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition group">
                    <i class="fas fa-home text-purple-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-purple-600">Dashboard</span>
                    <i class="fas fa-arrow-right ml-auto text-purple-600"></i>
                </a>
                <a href="{{ route('courier.assignments.index') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
                    <i class="fas fa-tasks text-blue-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-blue-600">My Assignments</span>
                    <i class="fas fa-arrow-right ml-auto text-blue-600"></i>
                </a>
                @else
                <a href="{{ route('client.dashboard') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition group">
                    <i class="fas fa-tachometer-alt text-green-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-green-600">Dashboard</span>
                    <i class="fas fa-arrow-right ml-auto text-green-600"></i>
                </a>
                <a href="{{ route('client.requests.index') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
                    <i class="fas fa-box text-blue-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-blue-600">My Requests</span>
                    <i class="fas fa-arrow-right ml-auto text-blue-600"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection