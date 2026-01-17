@extends('layouts.admin')

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
        <div class="card p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        @if(auth()->user()->profile_image)
                            <img src="{{ Storage::url(auth()->user()->profile_image) }}" 
                                 alt="{{ auth()->user()->full_name }}"
                                 class="w-20 h-20 rounded-full border-4 border-white shadow-lg">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=0D8ABC&color=fff&size=128" 
                                 alt="{{ auth()->user()->full_name }}"
                                 class="w-20 h-20 rounded-full border-4 border-white shadow-lg">
                        @endif
                        <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ auth()->user()->full_name }}</h2>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                @if(auth()->user()->isAdmin())
                                    Administrator
                                @elseif(auth()->user()->isCourier())
                                    Courier
                                @else
                                    Client
                                @endif
                            </span>
                            <span class="text-sm text-gray-500">ID: {{ auth()->user()->id }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.profile.edit') }}" class="btn-primary mt-4 md:mt-0">
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
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-purple-700">Total Requests</p>
                    <p class="text-2xl font-bold text-purple-900">
                        @if(auth()->user()->isAdmin())
                            {{ \App\Models\SpecimenRequest::count() }}
                        @elseif(auth()->user()->isCourier())
                            {{ auth()->user()->assignedRequests()->count() }}
                        @else
                            {{ auth()->user()->specimenRequests()->count() }}
                        @endif
                    </p>
                </div>
                <div class="bg-teal-50 p-4 rounded-lg">
                    <p class="text-sm text-teal-700">Completed</p>
                    <p class="text-2xl font-bold text-teal-900">
                        @if(auth()->user()->isAdmin())
                            {{ \App\Models\SpecimenRequest::where('status', 'completed')->count() }}
                        @elseif(auth()->user()->isCourier())
                            {{ auth()->user()->assignedRequests()->where('status', 'completed')->count() }}
                        @else
                            {{ auth()->user()->specimenRequests()->where('status', 'completed')->count() }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Personal Information</h4>
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="text-gray-600 w-32">Full Name:</span>
                            <span class="font-medium">{{ auth()->user()->full_name }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Email:</span>
                            <span class="font-medium">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Phone:</span>
                            <span class="font-medium">{{ auth()->user()->phone ?? 'Not set' }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Account Information</h4>
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="text-gray-600 w-32">Role:</span>
                            <span class="font-medium">{{ auth()->user()->role->name }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Status:</span>
                            <span class="font-medium {{ auth()->user()->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ auth()->user()->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Last Login:</span>
                            <span class="font-medium">
                                {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Never' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Recent Activity</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="space-y-4">
                @php
                    $activities = [
                        ['icon' => 'fa-user-plus', 'text' => 'Updated profile information', 'time' => '2 hours ago'],
                        ['icon' => 'fa-cog', 'text' => 'Changed password', 'time' => '1 day ago'],
                        ['icon' => 'fa-bell', 'text' => 'Updated notification settings', 'time' => '3 days ago'],
                        ['icon' => 'fa-chart-bar', 'text' => 'Viewed reports', 'time' => '1 week ago'],
                    ];
                @endphp
                @foreach($activities as $activity)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas {{ $activity['icon'] }} text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">{{ $activity['text'] }}</p>
                            <p class="text-sm text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Account Status -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Account Status</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span>Account Verified</span>
                    </div>
                    <span class="text-green-600 font-medium">Yes</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                        <span>Two-Factor Auth</span>
                    </div>
                    <span class="text-gray-600">Disabled</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>
                        <span>Member Since</span>
                    </div>
                    <span class="font-medium">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Security</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.profile.edit') }}" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-key text-gray-600 mr-3"></i>
                    <span>Change Password</span>
                </a>
                <a href="#" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-shield-alt text-gray-600 mr-3"></i>
                    <span>Two-Factor Authentication</span>
                </a>
                <a href="#" class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-history text-gray-600 mr-3"></i>
                    <span>Login History</span>
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Quick Links</h3>
            <div class="space-y-3">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                    <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition">
                    <i class="fas fa-chart-bar text-green-600 mr-3"></i>
                    <span>Reports</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                    <i class="fas fa-cog text-purple-600 mr-3"></i>
                    <span>Settings</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection