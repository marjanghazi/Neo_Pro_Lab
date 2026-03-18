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
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        @if(auth()->user()->profile_image)
                        <img src="{{ Storage::url(auth()->user()->profile_image) }}"
                            alt="{{ auth()->user()->full_name }}"
                            class="w-16 h-16 rounded-full border-2 border-white shadow-sm">
                        @else
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&size=128"
                            alt="{{ auth()->user()->full_name }}"
                            class="w-16 h-16 rounded-full border-2 border-white shadow-sm">
                        @endif
                        <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ auth()->user()->full_name }}</h2>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-0.5 text-xs rounded-full 
                                @if(auth()->user()->isAdmin()) bg-blue-100 text-blue-700
                                @elseif(auth()->user()->isCourier()) bg-purple-100 text-purple-700
                                @else bg-green-100 text-green-700
                                @endif">
                                @if(auth()->user()->isAdmin())
                                <i class="fas fa-crown mr-1"></i>Admin
                                @elseif(auth()->user()->isCourier())
                                <i class="fas fa-motorcycle mr-1"></i>Courier
                                @else
                                <i class="fas fa-user mr-1"></i>Client
                                @endif
                            </span>
                            <span class="text-xs text-gray-500">ID: {{ auth()->user()->id }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}"
                    class="mt-4 md:mt-0 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors inline-flex items-center text-sm">
                    <i class="fas fa-edit mr-2"></i> Edit Profile
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                @if(auth()->user()->isAdmin())
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Facilities</p>
                    <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Facility::count() }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Couriers</p>
                    <p class="text-lg font-semibold text-gray-900">{{ \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'courier'))->count() }}</p>
                </div>
                @endif

                @if(auth()->user()->isCourier())
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Active</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ auth()->user()->assignedRequests()->whereIn('status', ['accepted_by_courier', 'picked_up', 'in_transit'])->count() }}
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Completed</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ auth()->user()->assignedRequests()->where('status', 'completed')->count() }}
                    </p>
                </div>
                @endif

                @if(auth()->user()->isClient())
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Total</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ auth()->user()->createdRequests()->count() }}
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Completed</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ auth()->user()->createdRequests()->where('status', 'completed')->count() }}
                    </p>
                </div>
                @endif

                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Joined</p>
                    <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->created_at->format('M Y') }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Last Login</p>
                    <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Today' }}</p>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-user-circle text-teal-600 mr-2"></i>
                        Personal
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                            <span class="text-gray-600 w-24">Full Name:</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->full_name }}</span>
                        </div>
                        <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                            <span class="text-gray-600 w-24">Email:</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                            <span class="text-gray-600 w-24">Phone:</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->phone ?? 'Not set' }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-shield-alt text-teal-600 mr-2"></i>
                        Account
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                            <span class="text-gray-600 w-24">Role:</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->role->name }}</span>
                        </div>
                        <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                            <span class="text-gray-600 w-24">Status:</span>
                            <span class="font-medium {{ auth()->user()->is_active ? 'text-green-600' : 'text-red-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->is_active ? 'bg-green-500' : 'bg-red-500' }} inline-block mr-1.5"></span>
                                {{ auth()->user()->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                            <span class="text-gray-600 w-24">Verified:</span>
                            <span class="font-medium {{ auth()->user()->email_verified_at ? 'text-green-600' : 'text-yellow-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->email_verified_at ? 'bg-green-500' : 'bg-yellow-500' }} inline-block mr-1.5"></span>
                                {{ auth()->user()->email_verified_at ? 'Verified' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isCourier() && auth()->user()->vehicle_type)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-truck text-teal-600 mr-2"></i>
                    Vehicle
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                        <span class="text-gray-600 w-32">Vehicle Type:</span>
                        <span class="font-medium text-gray-800">{{ ucfirst(auth()->user()->vehicle_type) }}</span>
                    </div>
                    <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                        <span class="text-gray-600 w-32">Vehicle Number:</span>
                        <span class="font-medium text-gray-800">{{ auth()->user()->vehicle_number ?? 'Not set' }}</span>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->isClient())
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-building text-teal-600 mr-2"></i>
                    Facility
                </h4>
                @php
                $facility = auth()->user()->facilities()->first();
                @endphp
                @if($facility)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                        <span class="text-gray-600 w-32">Facility Name:</span>
                        <span class="font-medium text-gray-800">{{ $facility->name }}</span>
                    </div>
                    <div class="flex items-center p-2 bg-gray-50 rounded text-sm">
                        <span class="text-gray-600 w-32">Status:</span>
                        <span class="font-medium {{ $facility->is_approved ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $facility->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                </div>
                @else
                <div class="p-2 bg-yellow-50 rounded text-sm text-yellow-700">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    No facility assigned
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Account Status -->
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold mb-3 flex items-center">
                <i class="fas fa-shield-alt text-teal-600 mr-2"></i>
                Account Status
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="text-gray-700">Verified</span>
                    </div>
                    <span class="text-green-600 text-xs">
                        <i class="fas fa-check"></i>
                    </span>
                </div>
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>
                        <span class="text-gray-700">Member Since</span>
                    </div>
                    <span class="text-xs text-gray-700">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold mb-3 flex items-center">
                <i class="fas fa-lock text-teal-600 mr-2"></i>
                Security
            </h3>
            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}#password"
                    class="flex items-center p-2 hover:bg-gray-50 rounded transition text-sm group">
                    <i class="fas fa-key text-gray-500 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Change Password</span>
                    <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs group-hover:text-teal-600"></i>
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold mb-3 flex items-center">
                <i class="fas fa-link text-teal-600 mr-2"></i>
                Quick Links
            </h3>
            <div class="space-y-1">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 hover:bg-gray-50 rounded transition text-sm group">
                    <i class="fas fa-tachometer-alt text-gray-500 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Dashboard</span>
                    <i class="fas fa-arrow-right ml-auto text-gray-400 text-xs group-hover:text-teal-600"></i>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center p-2 hover:bg-gray-50 rounded transition text-sm group">
                    <i class="fas fa-users text-gray-500 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Users</span>
                    <i class="fas fa-arrow-right ml-auto text-gray-400 text-xs group-hover:text-teal-600"></i>
                </a>
                @elseif(auth()->user()->isCourier())
                <a href="{{ route('courier.dashboard') }}" class="flex items-center p-2 hover:bg-gray-50 rounded transition text-sm group">
                    <i class="fas fa-home text-gray-500 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Dashboard</span>
                    <i class="fas fa-arrow-right ml-auto text-gray-400 text-xs group-hover:text-teal-600"></i>
                </a>
                <a href="{{ route('courier.assignments.index') }}" class="flex items-center p-2 hover:bg-gray-50 rounded transition text-sm group">
                    <i class="fas fa-tasks text-gray-500 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Assignments</span>
                    <i class="fas fa-arrow-right ml-auto text-gray-400 text-xs group-hover:text-teal-600"></i>
                </a>
                @else
                <a href="{{ route('client.dashboard') }}" class="flex items-center p-2 hover:bg-gray-50 rounded transition text-sm group">
                    <i class="fas fa-tachometer-alt text-gray-500 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Dashboard</span>
                    <i class="fas fa-arrow-right ml-auto text-gray-400 text-xs group-hover:text-teal-600"></i>
                </a>
                <a href="{{ route('client.requests.index') }}" class="flex items-center p-2 hover:bg-gray-50 rounded transition text-sm group">
                    <i class="fas fa-box text-gray-500 group-hover:text-teal-600 mr-3"></i>
                    <span class="text-gray-700 group-hover:text-teal-600">Requests</span>
                    <i class="fas fa-arrow-right ml-auto text-gray-400 text-xs group-hover:text-teal-600"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection