@extends('layouts.admin')

@section('title', 'Manage Facilities')
@section('page-title', 'Facilities Management')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="#" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Facilities</a>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">All Facilities</h2>
            <p class="text-sm text-gray-600">Manage healthcare facilities and their information</p>
        </div>
        <a href="{{ route('admin.facilities.create') }}" class="btn-primary flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Facility
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <p class="text-sm text-blue-700">Total Facilities</p>
            <p class="text-2xl font-bold text-blue-900">{{ $facilities->total() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <p class="text-sm text-green-700">Active</p>
            <p class="text-2xl font-bold text-green-900">
                {{ \App\Models\Facility::where('status', 'active')->count() }}
            </p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <p class="text-sm text-yellow-700">Pending Approval</p>
            <p class="text-2xl font-bold text-yellow-900">
                {{ \App\Models\Facility::where('status', 'pending')->count() }}
            </p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-100">
            <p class="text-sm text-red-700">Suspended</p>
            <p class="text-2xl font-bold text-red-900">
                {{ \App\Models\Facility::where('status', 'suspended')->count() }}
            </p>
        </div>
    </div>

    <!-- Search and Filter -->
    <form method="GET" action="{{ route('admin.facilities.index') }}" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search facilities by name or license number..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
            <div>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="btn-primary px-6 py-2">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.facilities.index') }}" class="btn-secondary px-6 py-2">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            </div>
        </div>
    </form>

    <!-- Facilities Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Facility</th>
                    <th>Contact Information</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Approval</th>
                    <th>Users</th>
                    <th>Requests</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities as $facility)
                <tr>
                    <td>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-hospital text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium">{{ $facility->name }}</p>
                                <p class="text-xs text-gray-500">License: {{ $facility->license_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="space-y-1">
                            <p class="text-sm">{{ $facility->phone }}</p>
                            <p class="text-sm text-gray-500">{{ $facility->email }}</p>
                        </div>
                    </td>
                    <td>
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ $facility->facilityType->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'inactive' => 'bg-gray-100 text-gray-800',
                                'suspended' => 'bg-red-100 text-red-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$facility->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($facility->status) }}
                        </span>
                    </td>
                    <td>
                        @if($facility->is_approved)
                        <div class="flex items-center text-green-600">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="text-sm">Approved</span>
                        </div>
                        @else
                        <div class="flex items-center text-yellow-600">
                            <i class="fas fa-clock mr-2"></i>
                            <span class="text-sm">Pending</span>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="text-center">
                            <span class="text-lg font-bold">{{ $facility->users_count ?? 0 }}</span>
                            <p class="text-xs text-gray-500">Users</p>
                        </div>
                    </td>
                    <td>
                        <div class="text-center">
                            <span class="text-lg font-bold">{{ $facility->specimen_requests_count ?? 0 }}</span>
                            <p class="text-xs text-gray-500">Requests</p>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.facilities.show', $facility) }}" 
                               class="text-blue-600 hover:text-blue-800 p-1"
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.facilities.edit', $facility) }}" 
                               class="text-teal-600 hover:text-teal-800 p-1"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$facility->is_approved && $facility->status == 'pending')
                            <form action="{{ route('admin.facilities.approve', $facility) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-800 p-1"
                                        title="Approve"
                                        onclick="return confirm('Approve this facility?')">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.facilities.reject', $facility) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 p-1"
                                        title="Reject"
                                        onclick="return confirm('Reject this facility?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                            @if($facility->is_approved && $facility->status == 'active')
                            <a href="#" 
                               class="text-purple-600 hover:text-purple-800 p-1"
                               title="Suspend">
                                <i class="fas fa-pause"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-hospital text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 text-lg">No facilities found</p>
                            @if(request('search') || request('status'))
                                <p class="text-gray-400">Try adjusting your search or filter criteria</p>
                                <a href="{{ route('admin.facilities.index') }}" class="btn-primary mt-4">
                                    <i class="fas fa-times mr-2"></i> Clear Filters
                                </a>
                            @else
                                <p class="text-gray-400">Get started by adding a new facility</p>
                                <a href="{{ route('admin.facilities.create') }}" class="btn-primary mt-4">
                                    <i class="fas fa-plus mr-2"></i> Add New Facility
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($facilities->hasPages())
    <div class="mt-6">
        {{ $facilities->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="card p-6 mt-6">
    <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.facilities.create') }}" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                    <i class="fas fa-plus text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-blue-800">Add New Facility</h4>
                    <p class="text-sm text-blue-600">Register a new healthcare facility</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.facilities.index', ['status' => 'pending']) }}" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hover:bg-yellow-100 transition">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-yellow-800">Pending Approvals</h4>
                    <p class="text-sm text-yellow-600">{{ \App\Models\Facility::where('status', 'pending')->count() }} facilities waiting</p>
                </div>
            </div>
        </a>
        <a href="#" class="bg-purple-50 border border-purple-200 rounded-lg p-4 hover:bg-purple-100 transition">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                    <i class="fas fa-file-export text-purple-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-purple-800">Export Data</h4>
                    <p class="text-sm text-purple-600">Export facilities list to CSV/Excel</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection