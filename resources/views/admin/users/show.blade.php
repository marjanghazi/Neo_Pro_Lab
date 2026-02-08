@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.users.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Users</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Details</span>
    </div>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-2 space-y-6">
        <!-- User Profile Card -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <img src="https://ui-avatars.com/api/?name={{ $user->first_name }}+{{ $user->last_name }}&background=0D8ABC&color=fff" 
                         alt="{{ $user->full_name }}" class="w-16 h-16 rounded-full">
                    <div>
                        <h2 class="text-xl font-bold">{{ $user->full_name }}</h2>
                        <div class="flex items-center space-x-3 mt-2">
                            <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="badge badge-primary">
                                {{ $user->role->name }}
                            </span>
                            @if(!$user->isAdmin())
                            <span class="badge badge-{{ $user->is_approved ? 'success' : 'warning' }}">
                                {{ $user->is_approved ? 'Approved' : 'Pending Approval' }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.users.edit', $user) }}" 
                       class="btn-primary">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Contact Information</h3>
                    <div class="space-y-2">
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
                        <p><strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                        <p><strong>Last Login:</strong> 
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                Never logged in
                            @endif
                        </p>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Account Status</h3>
                    <div class="space-y-2">
                        <p><strong>Email Verified:</strong> 
                            @if($user->email_verified_at)
                                <span class="text-green-600">Yes ({{ $user->email_verified_at->format('M d, Y') }})</span>
                            @else
                                <span class="text-red-600">No</span>
                            @endif
                        </p>
                        <p><strong>Approval Status:</strong> 
                            @if($user->isAdmin())
                                <span class="text-blue-600">Auto-approved (Admin)</span>
                            @elseif($user->is_approved)
                                <span class="text-green-600">Approved</span>
                            @else
                                <span class="text-yellow-600">Pending Approval</span>
                            @endif
                        </p>
                        <p><strong>Account Created:</strong> {{ $user->created_at->format('F d, Y \a\t h:i A') }}</p>
                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Approval Actions (for non-admin, pending users) -->
            @if(!$user->isAdmin() && !$user->is_approved)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="font-medium text-gray-700 mb-3">Approval Actions</h3>
                <div class="flex space-x-3">
                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="btn-success"
                                onclick="return confirm('Approve {{ $user->full_name }}?')">
                            <i class="fas fa-check-circle mr-2"></i> Approve User
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn-danger"
                                onclick="return confirm('Reject {{ $user->full_name }}? This will delete the user.')">
                            <i class="fas fa-times-circle mr-2"></i> Reject User
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Activity Log -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-6">Recent Activity</h3>
            
            <div class="space-y-4">
                @php
                    $activities = \App\Models\AuditLog::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                @endphp
                
                @forelse($activities as $activity)
                <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ 
                            $activity->action == 'created' ? 'plus' : 
                            ($activity->action == 'updated' ? 'edit' : 
                            ($activity->action == 'deleted' ? 'trash' : 
                            ($activity->action == 'login' ? 'sign-in-alt' : 'bell')))
                        }} text-gray-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">{{ $activity->action }} {{ $activity->model_type }}</p>
                        @if($activity->changes)
                        <p class="text-sm text-gray-600">{{ json_encode($activity->changes, JSON_PRETTY_PRINT) }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-history text-3xl mb-2"></i>
                    <p>No activity found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Quick Stats -->
        <div class="card p-6">
            <h3 class="font-bold mb-4">Quick Stats</h3>
            
            <div class="space-y-4">
                @if($user->isCourier())
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-3xl font-bold text-blue-600">{{ $user->assignedRequests()->count() }}</p>
                    <p class="text-sm text-gray-600">Total Assignments</p>
                </div>
                
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-3xl font-bold text-green-600">{{ $user->assignedRequests()->where('status', 'completed')->count() }}</p>
                    <p class="text-sm text-gray-600">Completed</p>
                </div>
                @endif
                
                @if($user->isClient())
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-3xl font-bold text-purple-600">{{ $user->createdRequests()->count() }}</p>
                    <p class="text-sm text-gray-600">Total Requests</p>
                </div>
                
                <div class="text-center p-4 bg-teal-50 rounded-lg">
                    <p class="text-3xl font-bold text-teal-600">{{ $user->createdRequests()->where('status', 'completed')->count() }}</p>
                    <p class="text-sm text-gray-600">Completed</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Associated Facilities -->
        @if($user->facilities()->exists())
        <div class="card p-6">
            <h3 class="font-bold mb-4">Associated Facilities</h3>
            
            <div class="space-y-3">
                @foreach($user->facilities as $facility)
                <div class="p-3 border border-gray-200 rounded-lg">
                    <p class="font-medium">{{ $facility->name }}</p>
                    <p class="text-sm text-gray-600">{{ $facility->facility_type }}</p>
                    <div class="mt-2 text-xs">
                        <span class="px-2 py-1 bg-gray-100 rounded">{{ $facility->pivot->position }}</span>
                        @if($facility->pivot->is_primary_contact)
                        <span class="px-2 py-1 bg-teal-100 text-teal-700 rounded ml-2">Primary Contact</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Danger Zone -->
        <div class="card p-6 border border-red-200">
            <h3 class="font-bold text-red-700 mb-4">Danger Zone</h3>
            
            <div class="space-y-3">
                <!-- Approval Actions for non-approved users -->
                @if(!$user->isAdmin() && !$user->is_approved)
                <form action="{{ route('admin.users.approve', $user) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-green-50 border border-green-300 text-green-700 rounded-lg hover:bg-green-100 text-left">
                        <i class="fas fa-check-circle mr-2"></i>
                        Approve User
                    </button>
                </form>
                
                <form action="{{ route('admin.users.reject', $user) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-50 border border-red-300 text-red-700 rounded-lg hover:bg-red-100 text-left"
                            onclick="return confirm('Reject this user?')">
                        <i class="fas fa-times-circle mr-2"></i>
                        Reject User
                    </button>
                </form>
                @endif
                
                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-2 border border-{{ $user->is_active ? 'red' : 'green' }}-300 text-{{ $user->is_active ? 'red' : 'green' }}-700 rounded-lg hover:bg-{{ $user->is_active ? 'red' : 'green' }}-50 text-left">
                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check-circle' }} mr-2"></i>
                        {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                    </button>
                </form>
                
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 text-left">
                        <i class="fas fa-trash mr-2"></i>
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection