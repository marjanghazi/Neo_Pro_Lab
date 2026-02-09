@extends('layouts.admin')

@section('title', 'Assign Users to Facility')
@section('page-title', 'Assign Users to Facility')

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
        <a href="{{ route('admin.facilities.users.index', $facility) }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2 transition-colors duration-200">Manage Users</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Assign Users</span>
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
                    <i class="fas fa-user-plus text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Assign Users to: {{ $facility->name }}</h2>
                    <p class="text-gray-600">Select users to assign to this facility</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.facilities.users.index', $facility) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.facilities.users.assign', $facility) }}" method="POST" class="p-6">
        @csrf
        
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Available Users</h3>
            <p class="text-sm text-gray-600 mb-4">Select users to assign to this facility:</p>
            
            @if($availableUsers->count() > 0)
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2">
                    @foreach($availableUsers as $user)
                    <div class="user-select-item bg-white border border-gray-300 rounded-lg p-4 hover:border-blue-400 hover:shadow-sm transition-all duration-200">
                        <div class="flex items-start space-x-3">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       id="user_{{ $user->id }}" 
                                       name="user_ids[]" 
                                       value="{{ $user->id }}"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                       {{ in_array($user->id, $currentUsers) ? 'checked' : '' }}>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full" 
                                             src="https://ui-avatars.com/api/?name={{ $user->first_name }}+{{ $user->last_name }}&background=0D8ABC&color=fff" 
                                             alt="{{ $user->full_name }}">
                                        <div class="ml-3">
                                            <label for="user_{{ $user->id }}" class="text-sm font-medium text-gray-900 cursor-pointer">
                                                {{ $user->full_name }}
                                            </label>
                                            <p class="text-xs text-gray-500">{{ $user->role->name }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 space-y-1 mb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2 text-xs"></i>
                                        <span class="truncate">{{ $user->email }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-2 text-xs"></i>
                                        <span>{{ $user->phone }}</span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 mt-3">
                                    <div>
                                        <label for="position_{{ $user->id }}" class="block text-xs font-medium text-gray-700 mb-1">Position</label>
                                        <input type="text" 
                                               id="position_{{ $user->id }}" 
                                               name="positions[{{ $user->id }}]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., Lab Manager">
                                    </div>
                                    <div>
                                        <label for="department_{{ $user->id }}" class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                                        <input type="text" 
                                               id="department_{{ $user->id }}" 
                                               name="departments[{{ $user->id }}]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., Pathology">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-4 flex items-center text-sm text-gray-500">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                <span>Selected users will be assigned to this facility with the specified roles</span>
            </div>
            @else
            <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-users text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No available users</h3>
                <p class="text-gray-500 mb-4">All active users are already assigned to this facility.</p>
                <a href="{{ route('admin.facilities.users.index', $facility) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Users
                </a>
            </div>
            @endif
        </div>

        @if($availableUsers->count() > 0)
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
            <a href="{{ route('admin.facilities.users.index', $facility) }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 inline-flex items-center justify-center">
                <i class="fas fa-save mr-2"></i> Assign Selected Users
            </button>
        </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox
        const selectAllBtn = document.getElementById('select-all');
        const userCheckboxes = document.querySelectorAll('input[name="user_ids[]"]');
        
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const isChecked = this.checked;
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
            });
        }
        
        // Individual checkbox click
        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('click', function() {
                const allChecked = Array.from(userCheckboxes).every(cb => cb.checked);
                if (selectAllBtn) {
                    selectAllBtn.checked = allChecked;
                }
            });
        });
        
        // Show/hide position/department fields based on checkbox
        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const userId = this.value;
                const positionField = document.getElementById(`position_${userId}`);
                const departmentField = document.getElementById(`department_${userId}`);
                
                if (positionField && departmentField) {
                    if (this.checked) {
                        positionField.disabled = false;
                        departmentField.disabled = false;
                    } else {
                        positionField.disabled = true;
                        departmentField.disabled = true;
                        positionField.value = '';
                        departmentField.value = '';
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection