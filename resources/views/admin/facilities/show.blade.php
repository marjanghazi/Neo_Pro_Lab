@extends('layouts.admin')

@section('title', 'Facility Details')
@section('page-title', 'Facility Details')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.facilities.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Facilities</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">{{ $facility->name }}</span>
    </div>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Facility Header -->
        <div class="card p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-hospital text-3xl text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $facility->name }}</h2>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="px-3 py-1 text-sm rounded-full {{ $facility->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($facility->status) }}
                            </span>
                            @if($facility->is_approved)
                            <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-check-circle mr-1"></i> Approved
                            </span>
                            @endif
                            <span class="text-gray-500 text-sm">
                                ID: {{ $facility->id }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-2">
                    <a href="{{ route('admin.facilities.edit', $facility) }}"
                        class="btn-primary">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    @if(!$facility->is_approved && $facility->status == 'pending')
                    <form action="{{ route('admin.facilities.approve', $facility) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="btn-secondary bg-green-100 text-green-700 border-green-300 hover:bg-green-200">
                            <i class="fas fa-check mr-2"></i> Approve
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-700">Total Users</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-700">Total Requests</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['total_requests'] }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-sm text-yellow-700">Active Requests</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $stats['active_requests'] }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-purple-700">Completed</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $stats['completed_requests'] }}</p>
                </div>
            </div>

            <!-- Facility Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Facility Information</h4>
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="text-gray-600 w-32">Type:</span>
                            <span class="font-medium">{{ $facility->facilityType->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">License No:</span>
                            <span class="font-medium">{{ $facility->license_number }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Operating Hours:</span>
                            <span class="font-medium">{{ $facility->operating_hours ?? 'Not specified' }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Contact Information</h4>
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="text-gray-600 w-32">Phone:</span>
                            <span class="font-medium">{{ $facility->phone }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Email:</span>
                            <span class="font-medium">{{ $facility->email }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Website:</span>
                            <span class="font-medium">
                                @if($facility->website)
                                <a href="{{ $facility->website }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $facility->website }}
                                </a>
                                @else
                                Not specified
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Address Information</h3>
            <div class="space-y-2">
                <div class="flex">
                    <span class="text-gray-600 w-24">Address:</span>
                    <span class="font-medium">{{ $facility->address }}</span>
                </div>
                <div class="flex">
                    <span class="text-gray-600 w-24">City:</span>
                    <span class="font-medium">{{ $facility->city }}</span>
                </div>
                <div class="flex">
                    <span class="text-gray-600 w-24">State:</span>
                    <span class="font-medium">{{ $facility->state }}</span>
                </div>
                <div class="flex">
                    <span class="text-gray-600 w-24">ZIP Code:</span>
                    <span class="font-medium">{{ $facility->zip_code }}</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Facility Location Map</p>
                        <p class="text-sm text-gray-500 mt-1">Map integration available</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Recent Specimen Requests</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            @if($facility->specimenRequests->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facility->specimenRequests as $request)
                        <tr>
                            <td class="font-mono text-sm">#{{ $request->id }}</td>
                            <td>{{ $request->patient_name }}</td>
                            <td>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $request->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($request->status == 'in_transit' ? 'bg-blue-100 text-blue-800' : 
                                       ($request->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td class="text-sm">{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="#" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No specimen requests found</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Contact Person -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Primary Contact Person</h3>
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user text-blue-600"></i>
                </div>
                <div>
                    <p class="font-medium">{{ $facility->contact_person_name }}</p>
                    <p class="text-sm text-gray-500">Primary Contact</p>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center">
                    <i class="fas fa-phone text-gray-400 w-5 mr-2"></i>
                    <span class="text-sm">{{ $facility->contact_person_phone }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-envelope text-gray-400 w-5 mr-2"></i>
                    <span class="text-sm">{{ $facility->contact_person_email }}</span>
                </div>
            </div>
        </div>

        <!-- Facility Users -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-bold">Facility Users</h3>
                    <p class="text-sm text-gray-500">{{ $stats['total_users'] }} user(s) assigned</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.facilities.users.index', $facility) }}"
                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 inline-flex items-center text-sm">
                        <i class="fas fa-user-cog mr-2"></i> Manage Users
                    </a>
                </div>
            </div>

            @if($facility->users->count() > 0)
            <div class="space-y-3">
                @foreach($facility->users->take(5) as $user)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <img src="https://ui-avatars.com/api/?name={{ $user->first_name }}+{{ $user->last_name }}&background=0D8ABC&color=fff"
                            alt="{{ $user->full_name }}"
                            class="w-8 h-8 rounded-full">
                        <div>
                            <p class="font-medium text-sm">{{ $user->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->role->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($user->pivot->is_primary_contact)
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-star mr-1"></i> Primary
                        </span>
                        @endif
                        <span class="text-xs px-2 py-1 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            @if($facility->users->count() > 5)
            <div class="mt-4 text-center">
                <a href="{{ route('admin.facilities.users.index', $facility) }}" class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center">
                    View all {{ $facility->users->count() }} users <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endif

            @else
            <div class="text-center py-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-users text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 mb-4">No users assigned to this facility</p>
                <a href="{{ route('admin.facilities.users.assign.form', $facility) }}"
                    class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 inline-flex items-center text-sm">
                    <i class="fas fa-user-plus mr-2"></i> Assign Users
                </a>
            </div>
            @endif
        </div>

        <!-- Approval Information -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Approval Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Approval Status:</span>
                    <span class="font-medium {{ $facility->is_approved ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $facility->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>
                @if($facility->is_approved)
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Approved By:</span>
                        <span class="font-medium">{{ $facility->approver->full_name ?? 'System' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Approved At:</span>
                        <span class="font-medium">
                            {{ $facility->approved_at ? $facility->approved_at->format('M d, Y H:i') : 'Not recorded' }}
                        </span>
                    </div>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Created:</span>
                    <span class="font-medium">{{ $facility->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Last Updated:</span>
                    <span class="font-medium">{{ $facility->updated_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($facility->notes)
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Notes</h3>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-gray-700">{{ $facility->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.facilities.edit', $facility) }}"
                    class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                    <i class="fas fa-edit text-blue-600 mr-3"></i>
                    <span>Edit Facility Information</span>
                </a>
                @if(!$facility->is_approved)
                <form action="{{ route('admin.facilities.approve', $facility) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition text-left">
                        <i class="fas fa-check text-green-600 mr-3"></i>
                        <span>Approve Facility</span>
                    </button>
                </form>
                @endif
                @if($facility->is_approved && $facility->status == 'active')
                <a href="#"
                    class="flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition">
                    <i class="fas fa-pause text-yellow-600 mr-3"></i>
                    <span>Suspend Facility</span>
                </a>
                @endif
                @if($facility->status != 'active')
                <a href="#"
                    class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition">
                    <i class="fas fa-play text-green-600 mr-3"></i>
                    <span>Activate Facility</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection