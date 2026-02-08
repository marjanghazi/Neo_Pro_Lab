@extends('layouts.admin')

@section('title', 'Pending Approvals')
@section('page-title', 'Pending User Approvals')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Pending Approvals</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">Pending User Approvals</h2>
            <p class="text-sm text-gray-600">Review and approve new user registrations</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary flex items-center">
                <i class="fas fa-users mr-2"></i> All Users
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($pendingUsers->isEmpty())
    <div class="text-center py-12">
        <i class="fas fa-user-check text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-600 mb-2">No Pending Approvals</h3>
        <p class="text-gray-500">All users have been reviewed and approved.</p>
    </div>
    @else
    <!-- Pending Users Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingUsers as $user)
                <tr class="hover:bg-yellow-50">
                    <td>
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name={{ $user->first_name }}+{{ $user->last_name }}&background=FFC107&color=000" 
                                 alt="{{ $user->full_name }}" class="w-8 h-8 rounded-full">
                            <div>
                                <p class="font-medium">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge 
                            @if($user->role->slug == 'courier') badge-info
                            @else badge-success @endif">
                            {{ $user->role->name }}
                        </span>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? 'N/A' }}</td>
                    <td class="text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                        <br>
                        <span class="text-xs">{{ $user->created_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <!-- View Details Button -->
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="text-teal-600 hover:text-teal-800 p-2 rounded-full hover:bg-teal-50"
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <!-- Approve Button -->
                            <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-800 p-2 rounded-full hover:bg-green-50"
                                        title="Approve User"
                                        onclick="return confirm('Approve {{ $user->full_name }}?')">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            </form>
                            
                            <!-- Reject Button -->
                            <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50"
                                        title="Reject User"
                                        onclick="return confirm('Reject {{ $user->full_name }}? This action cannot be undone.')">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                            
                            <!-- Delete Button -->
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-gray-600 hover:text-gray-800 p-2 rounded-full hover:bg-gray-100"
                                        title="Delete User"
                                        onclick="return confirm('Permanently delete {{ $user->full_name }}?')">
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
    @endif
</div>
@endsection