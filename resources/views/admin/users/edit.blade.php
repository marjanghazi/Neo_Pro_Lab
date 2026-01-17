@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Edit</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold">Edit User: {{ $user->full_name }}</h2>
            <div class="flex items-center space-x-2">
                <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="badge badge-primary">
                    {{ $user->role->name }}
                </span>
            </div>
        </div>
        
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" 
                           name="first_name" 
                           value="{{ old('first_name', $user->first_name) }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('first_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                    <input type="text" 
                           name="last_name" 
                           value="{{ old('last_name', $user->last_name) }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('last_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}"
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" 
                       name="phone" 
                       value="{{ old('phone', $user->phone) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                <select name="role_id" 
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" 
                           name="password" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Leave blank to keep current">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" 
                           name="password_confirmation" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    <span class="ml-2 text-sm text-gray-700">Active User</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="btn-primary px-6 py-2">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection