@extends('layouts.admin')

@section('title', 'Manage Facility Users')
@section('page-title', 'Manage Facility Users')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.facilities.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2 transition-colors duration-200">Facilities</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.facilities.show', $facility) }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2 transition-colors duration-200">{{ $facility->name }}</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Manage Users</span>
    </div>
</li>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 border border-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-8 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg shadow-sm border border-gray-200 mr-4">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Manage Users: {{ $facility->name }}</h2>
                    <p class="text-gray-600">Assign and manage users for this facility</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.facilities.show', $facility) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Facility
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Assigned Users</h3>
                <p class="text-sm text-gray-600">{{ $facility->users->count() }} user(s) assigned</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.facilities.users.assign.form', $facility) }}" 
                   class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 inline-flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i> Assign New Users
                </a>
            </div>
        </div>

        @if($facility->users->count() > 0)
        <!-- Users Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($facility->users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" 
                                         src="https://ui-avatars.com/api/?name={{ $user->first_name }}+{{ $user->last_name }}&background=0D8ABC&color=fff" 
                                         alt="{{ $user->full_name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($user->pivot->is_primary_contact)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-star mr-1 text-xs"></i> Primary Contact
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $user->role->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="space-y-1">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-400 mr-2 text-xs"></i>
                                    {{ $user->email }}
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-400 mr-2 text-xs"></i>
                                    {{ $user->phone }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @if(!$user->pivot->is_primary_contact)
                                <form action="{{ route('admin.facilities.users.toggle-primary', [$facility, $user]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded text-sm transition-colors duration-200">
                                        <i class="fas fa-star mr-1"></i> Set Primary
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('admin.facilities.users.detach', [$facility, $user]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to remove {{ $user->full_name }} from this facility?')"
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded text-sm transition-colors duration-200">
                                        <i class="fas fa-user-minus mr-1"></i> Remove
                                    </button>
                                </form>
                                
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1 rounded text-sm transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-users text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No users assigned</h3>
            <p class="text-gray-500 mb-6">Get started by assigning users to this facility.</p>
            <a href="{{ route('admin.facilities.users.assign.form', $facility) }}" 
               class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 inline-flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i> Assign Users
            </a>
        </div>
        @endif

        <!-- Available Users Section -->
        @if($availableUsers->count() > 0)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Available Users for Assignment</h3>
            <p class="text-sm text-gray-600 mb-4">These users are not currently assigned to this facility:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($availableUsers as $user)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full" 
                                 src="https://ui-avatars.com/api/?name={{ $user->first_name }}+{{ $user->last_name }}&background=0D8ABC&color=fff" 
                                 alt="{{ $user->full_name }}">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->role->name }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-400 mr-2 text-xs"></i>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 mr-2 text-xs"></i>
                            <span>{{ $user->phone }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <form action="{{ route('admin.facilities.users.assign', $facility) }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_ids[]" value="{{ $user->id }}">
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-white border border-blue-300 text-blue-700 font-medium rounded-lg hover:bg-blue-50 transition-colors duration-200 inline-flex items-center justify-center text-sm">
                                <i class="fas fa-plus mr-2"></i> Assign to Facility
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection