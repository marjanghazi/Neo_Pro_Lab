@extends('layouts.admin')

@section('title', 'Manage Couriers')
@section('page-title', 'Couriers Management')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="#" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Couriers</a>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">All Couriers</h2>
            <p class="text-sm text-gray-600">Manage delivery personnel and their assignments</p>
        </div>
        <a href="{{ route('admin.couriers.create') }}" class="btn-primary flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Courier
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <p class="text-sm text-blue-700">Total Couriers</p>
            <p class="text-2xl font-bold text-blue-900">{{ $couriers->total() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <p class="text-sm text-green-700">Active Today</p>
            <p class="text-2xl font-bold text-green-900">
                {{ \App\Models\User::whereHas('role', function($q) { $q->where('slug', 'courier'); })->where('is_active', true)->count() }}
            </p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
            <p class="text-sm text-purple-700">On Delivery</p>
            <p class="text-2xl font-bold text-purple-900">
                {{ \App\Models\User::whereHas('role', function($q) { $q->where('slug', 'courier'); })->whereHas('assignedRequests', function($q) { $q->whereIn('status', ['in_transit', 'picked_up']); })->count() }}
            </p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <p class="text-sm text-yellow-700">Available</p>
            <p class="text-2xl font-bold text-yellow-900">
                {{ \App\Models\User::whereHas('role', function($q) { $q->where('slug', 'courier'); })->where('is_active', true)->whereDoesntHave('assignedRequests', function($q) { $q->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up']); })->count() }}
            </p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="md:col-span-2">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text" 
                       placeholder="Search couriers by name, email, or phone..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>
        </div>
        <div>
            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="available">Available</option>
                <option value="busy">Busy</option>
            </select>
        </div>
    </div>

    <!-- Couriers Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Courier</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Active Assignments</th>
                    <th>Performance</th>
                    <th>Last Activity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($couriers as $courier)
                <tr>
                    <td>
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0D8ABC&color=fff" 
                                 alt="{{ $courier->full_name }}" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-medium">{{ $courier->full_name }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $courier->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="space-y-1">
                            <p class="text-sm">{{ $courier->email }}</p>
                            <p class="text-sm text-gray-500">{{ $courier->phone ?? 'N/A' }}</p>
                        </div>
                    </td>
                    <td>
                        @if($courier->is_active)
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-green-700">Active</span>
                        </div>
                        @else
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            <span class="text-red-700">Inactive</span>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="text-center">
                            <span class="text-lg font-bold">{{ $courier->assigned_requests_count }}</span>
                            <p class="text-xs text-gray-500">Active</p>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                            <span class="text-sm font-medium">85%</span>
                        </div>
                    </td>
                    <td class="text-sm text-gray-500">
                        {{ $courier->last_login_at ? $courier->last_login_at->diffForHumans() : 'Never' }}
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.couriers.show', $courier) }}" 
                               class="text-blue-600 hover:text-blue-800 p-1"
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.couriers.edit', $courier) }}" 
                               class="text-teal-600 hover:text-teal-800 p-1"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="text-purple-600 hover:text-purple-800 p-1" title="Assign Delivery">
                                <i class="fas fa-tasks"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-gray-800 p-1" title="Send Message">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $couriers->links() }}
    </div>
</div>

<!-- Courier Locations Map -->
<div class="card p-6 mt-6">
    <h3 class="text-lg font-bold mb-4">Courier Locations</h3>
    <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center">
        <div class="text-center">
            <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">Live courier locations map</p>
            <p class="text-sm text-gray-500 mt-1">Real-time tracking integration</p>
        </div>
    </div>
</div>
@endsection