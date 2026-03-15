@extends('layouts.admin')

@section('title', 'Manage Facilities')
@section('page-title', 'Facilities')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="ml-1 text-sm text-gray-600">Facilities</span>
    </div>
</li>
@endsection

@section('content')
<!-- Main Card -->
<div class="bg-white rounded-lg border border-gray-200">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/30">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">All Facilities</h2>
                <p class="text-xs text-gray-500 mt-0.5">Manage healthcare facilities and their information</p>
            </div>
            <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium rounded-md transition-colors">
                <i class="fas fa-plus mr-1.5 text-xs"></i>
                Add Facility
            </a>
        </div>
    </div>

    <!-- Stats Overview - Compact -->
    <div class="p-5 border-b border-gray-100">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-[11px] font-medium text-blue-700 uppercase tracking-wider">Total</p>
                <p class="text-xl font-bold text-blue-900 mt-1">{{ $facilities->total() }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3">
                <p class="text-[11px] font-medium text-green-700 uppercase tracking-wider">Active</p>
                <p class="text-xl font-bold text-green-900 mt-1">{{ \App\Models\Facility::where('status', 'active')->count() }}</p>
            </div>
            <div class="bg-amber-50 rounded-lg p-3">
                <p class="text-[11px] font-medium text-amber-700 uppercase tracking-wider">Pending</p>
                <p class="text-xl font-bold text-amber-900 mt-1">{{ \App\Models\Facility::where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-3">
                <p class="text-[11px] font-medium text-red-700 uppercase tracking-wider">Suspended</p>
                <p class="text-xl font-bold text-red-900 mt-1">{{ \App\Models\Facility::where('status', 'suspended')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="p-5 border-b border-gray-100">
        <form method="GET" action="{{ route('admin.facilities.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by name or license number..." 
                               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-teal-500 focus:border-teal-500 placeholder:text-gray-400">
                    </div>
                </div>
                <div>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium rounded-md transition-colors">
                        <i class="fas fa-filter mr-1.5 text-xs"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.facilities.index') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-md transition-colors">
                        <i class="fas fa-times mr-1.5 text-xs"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Facilities Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/80">
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Facility</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Approval</th>
                    <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Users</th>
                    <th class="px-5 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Requests</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($facilities as $facility)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-50 rounded-md flex items-center justify-center mr-3">
                                <i class="fas fa-hospital text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $facility->name }}</p>
                                <p class="text-[11px] text-gray-500">License: {{ $facility->license_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div>
                            <p class="text-sm text-gray-700">{{ $facility->phone }}</p>
                            <p class="text-[11px] text-gray-500">{{ $facility->email }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                            {{ $facility->facilityType->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @php
                            $statusStyles = [
                                'active' => ['bg-green-50', 'text-green-700'],
                                'pending' => ['bg-amber-50', 'text-amber-700'],
                                'inactive' => ['bg-gray-50', 'text-gray-700'],
                                'suspended' => ['bg-red-50', 'text-red-700'],
                                'rejected' => ['bg-red-50', 'text-red-700'],
                            ];
                            $style = $statusStyles[$facility->status] ?? ['bg-gray-50', 'text-gray-700'];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $style[0] }} {{ $style[1] }}">
                            {{ ucfirst($facility->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($facility->is_approved)
                        <div class="flex items-center text-green-600">
                            <i class="fas fa-check-circle text-xs mr-1"></i>
                            <span class="text-xs">Approved</span>
                        </div>
                        @else
                        <div class="flex items-center text-amber-600">
                            <i class="fas fa-clock text-xs mr-1"></i>
                            <span class="text-xs">Pending</span>
                        </div>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-center">
                        <span class="text-sm font-semibold text-gray-900">{{ $facility->users_count ?? 0 }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-center">
                        <span class="text-sm font-semibold text-gray-900">{{ $facility->specimen_requests_count ?? 0 }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('admin.facilities.show', $facility) }}" 
                               class="w-7 h-7 inline-flex items-center justify-center rounded-md text-blue-600 hover:bg-blue-50 transition-colors"
                               title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('admin.facilities.edit', $facility) }}" 
                               class="w-7 h-7 inline-flex items-center justify-center rounded-md text-teal-600 hover:bg-teal-50 transition-colors"
                               title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            
                            @if(!$facility->is_approved && $facility->status == 'pending')
                            <form action="{{ route('admin.facilities.approve', $facility) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-md text-green-600 hover:bg-green-50 transition-colors"
                                        title="Approve"
                                        onclick="return confirm('Approve this facility?')">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.facilities.reject', $facility) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-md text-red-600 hover:bg-red-50 transition-colors"
                                        title="Reject"
                                        onclick="return confirm('Reject this facility?')">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </form>
                            @endif

                            @if($facility->is_approved && $facility->status == 'active')
                            <form action="{{ route('admin.facilities.suspend', $facility) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-md text-orange-600 hover:bg-orange-50 transition-colors"
                                        title="Suspend"
                                        onclick="return confirm('Suspend this facility?')">
                                    <i class="fas fa-pause text-xs"></i>
                                </button>
                            </form>
                            @endif

                            @if(in_array($facility->status, ['suspended', 'rejected']))
                            <form action="{{ route('admin.facilities.activate', $facility) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-md text-green-600 hover:bg-green-50 transition-colors"
                                        title="Activate"
                                        onclick="return confirm('Activate this facility?')">
                                    <i class="fas fa-play text-xs"></i>
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-md text-red-600 hover:bg-red-50 transition-colors"
                                        title="Delete"
                                        onclick="return confirm('Permanently delete this facility? This cannot be undone.')">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-hospital text-gray-400 text-lg"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900 mb-1">No facilities found</p>
                            @if(request('search') || request('status'))
                                <p class="text-xs text-gray-500 mb-3">Try adjusting your search or filter criteria</p>
                                <a href="{{ route('admin.facilities.index') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-times mr-1.5 text-xs"></i>
                                    Clear Filters
                                </a>
                            @else
                                <p class="text-xs text-gray-500 mb-3">Get started by adding a new facility</p>
                                <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium rounded-md">
                                    <i class="fas fa-plus mr-1.5 text-xs"></i>
                                    Add Facility
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
    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/30">
        {{ $facilities->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="mt-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <a href="{{ route('admin.facilities.create') }}" class="bg-white border border-gray-200 rounded-lg p-4 hover:border-teal-200 hover:bg-teal-50/30 transition-all group">
            <div class="flex items-center">
                <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-100 transition-colors">
                    <i class="fas fa-plus text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Add New Facility</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Register a new healthcare facility</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('admin.facilities.index', ['status' => 'pending']) }}" class="bg-white border border-gray-200 rounded-lg p-4 hover:border-amber-200 hover:bg-amber-50/30 transition-all group">
            <div class="flex items-center">
                <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-amber-100 transition-colors">
                    <i class="fas fa-clock text-amber-600 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Pending Approvals</h4>
                    <p class="text-xs text-gray-500 mt-0.5">{{ \App\Models\Facility::where('status', 'pending')->count() }} facilities waiting</p>
                </div>
            </div>
        </a>
        
        <button onclick="window.location.href='#'" class="bg-white border border-gray-200 rounded-lg p-4 hover:border-purple-200 hover:bg-purple-50/30 transition-all group">
            <div class="flex items-center">
                <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-100 transition-colors">
                    <i class="fas fa-file-export text-purple-600 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Export Data</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Export facilities list to CSV</p>
                </div>
            </div>
        </button>
    </div>
</div>

@push('styles')
<style>
    /* Status indicator colors */
    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-active { background-color: #10B981; }
    .status-pending { background-color: #F59E0B; }
    .status-suspended { background-color: #EF4444; }
    .status-rejected { background-color: #EF4444; }

    /* Table hover effects */
    tbody tr {
        transition: background-color 0.15s ease;
    }

    /* Quick action cards */
    .group {
        transition: all 0.2s ease;
    }

    /* Button hover states */
    .hover\:bg-blue-50, .hover\:bg-teal-50, .hover\:bg-green-50, 
    .hover\:bg-red-50, .hover\:bg-orange-50 {
        transition: background-color 0.15s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-submit form when status filter changes
    document.querySelector('select[name="status"]')?.addEventListener('change', function() {
        this.form.submit();
    });

    // Handle enter key in search
    document.querySelector('input[name="search"]')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });
</script>
@endpush
@endsection