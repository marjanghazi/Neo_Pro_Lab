@extends('layouts.admin')

@section('title', 'Manage Users')
@section('page-title', 'Users Management')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="#" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Users</a>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header with Search, Add Button, and Pending Approvals -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">All Users</h2>
            <p class="text-sm text-gray-600">Manage system users and their permissions</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Pending Approvals Button -->
            @php
                $pendingCount = \App\Models\User::where('is_approved', false)
                    ->whereHas('role', function($q) {
                        $q->where('slug', '!=', 'admin');
                    })
                    ->count();
            @endphp
            
            @if($pendingCount > 0)
            <a href="{{ route('admin.users.pending') }}" 
               class="btn-warning flex items-center relative">
                <i class="fas fa-user-clock mr-2"></i> 
                Pending Approvals
                <span class="ml-2 bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1">
                    {{ $pendingCount }}
                </span>
            </a>
            @endif
            
            <a href="{{ route('admin.users.create') }}" class="btn-primary flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New User
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="md:col-span-2">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text" 
                       placeholder="Search users by name, email, or phone..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>
        </div>
        <div>
            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="">All Roles</option>
                <option value="admin">Administrator</option>
                <option value="courier">Courier</option>
                <option value="client">Client</option>
            </select>
        </div>
        <div>
            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="">All Status</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending Approval</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Approval</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name={{ $user->first_name }}+{{ $user->last_name }}&background=0D8ABC&color=fff" 
                                 alt="{{ $user->full_name }}" class="w-8 h-8 rounded-full">
                            <div>
                                <p class="font-medium">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge 
                            @if($user->role->slug == 'admin') badge-primary
                            @elseif($user->role->slug == 'courier') badge-info
                            @else badge-success @endif">
                            {{ $user->role->name }}
                        </span>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? 'N/A' }}</td>
                    <td>
                        @if($user->is_active)
                        <span class="badge badge-success">
                            <i class="fas fa-circle text-xs mr-1"></i> Active
                        </span>
                        @else
                        <span class="badge badge-danger">
                            <i class="fas fa-circle text-xs mr-1"></i> Inactive
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($user->isAdmin())
                            <span class="badge badge-primary">
                                <i class="fas fa-shield-alt text-xs mr-1"></i> Auto
                            </span>
                        @elseif($user->is_approved)
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle text-xs mr-1"></i> Approved
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-clock text-xs mr-1"></i> Pending
                            </span>
                        @endif
                    </td>
                    <td class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="text-teal-600 hover:text-teal-800 p-1"
                               title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="text-blue-600 hover:text-blue-800 p-1"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <!-- Approve/Reject buttons for pending users -->
                            @if(!$user->is_approved && !$user->isAdmin())
                            <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-800 p-1"
                                        title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                            
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-{{ $user->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $user->is_active ? 'yellow' : 'green' }}-800 p-1"
                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check-circle' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection