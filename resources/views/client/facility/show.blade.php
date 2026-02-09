@extends('layouts.client')

@section('title', 'My Facility')
@section('page-title', 'My Facility Profile')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header with Back Button -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Facility Profile</h1>
            <p class="text-gray-600">View your facility information</p>
        </div>
        <a href="{{ route('client.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>

    <!-- Facility Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Facility Header -->
        <div class="bg-gradient-to-r from-teal-600 to-teal-800 p-6 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h2 class="text-2xl font-bold">{{ $facility->name }}</h2>
                    <div class="flex items-center mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-500 text-white">
                            <i class="fas fa-hospital mr-2"></i>
                            {{ ucfirst(str_replace('_', ' ', $facility->facility_type)) }}
                        </span>
                        <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            {{ $facility->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas fa-circle mr-2 text-xs"></i>
                            {{ ucfirst($facility->status) }}
                        </span>
                        @if($facility->is_approved)
                        <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            Approved
                        </span>
                        @endif
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="text-teal-200 text-sm">License Number</div>
                    <div class="text-lg font-mono font-bold">{{ $facility->license_number }}</div>
                </div>
            </div>
        </div>

        <!-- Facility Details -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column: Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-info-circle mr-2 text-teal-600"></i>Basic Information
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Address Information -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Address</h4>
                            <div class="space-y-1">
                                <p class="text-gray-900">{{ $facility->address }}</p>
                                <p class="text-gray-700">{{ $facility->city }}, {{ $facility->state }}</p>
                                <p class="text-gray-700">{{ $facility->country }} - {{ $facility->postal_code }}</p>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Primary Contact</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-teal-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $facility->contact_person_name }}</p>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                                {{ $facility->contact_person_email }}
                                            </p>
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-phone mr-2 text-gray-400"></i>
                                                {{ $facility->contact_person_phone }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Status & Additional Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-chart-line mr-2 text-teal-600"></i>Status & Approval
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Approval Status -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-900">Approval Details</h4>
                                @if($facility->is_approved)
                                    <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Verified
                                    </span>
                                @endif
                            </div>
                            
                            @if($facility->is_approved)
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Approved By:</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $facility->approver ? $facility->approver->name : 'System' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Approval Date:</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $facility->approved_at ? $facility->approved_at->format('M d, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="w-12 h-12 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-clock text-yellow-600"></i>
                                    </div>
                                    <p class="text-yellow-700 font-medium">Pending Approval</p>
                                    <p class="text-sm text-gray-600 mt-1">Your facility is under review</p>
                                </div>
                            @endif
                        </div>

                        <!-- Quick Stats -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Facility Details</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-users text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-2xl font-bold text-blue-900">{{ $facility->users->count() }}</p>
                                            <p class="text-sm text-blue-700">Associated Users</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-file-medical text-purple-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-2xl font-bold text-purple-900">{{ $facility->specimenRequests->count() }}</p>
                                            <p class="text-sm text-purple-700">Specimen Requests</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Date -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Registration Date</p>
                                    <p class="text-sm text-gray-600">When facility was registered</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $facility->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $facility->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes (Optional) -->
            @if($facility->facility_type == 'research_center')
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-flask text-teal-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-900">Research Center Information</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            This facility is registered as a research center. All specimen requests are processed 
                            with priority handling and additional quality control measures.
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-600 mb-4 sm:mb-0">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Facility information is read-only. Contact support for updates.
                </div>
                <div class="flex space-x-4">
                    <a href="mailto:support@neoprolab.com?subject=Update Request: {{ $facility->name }}" 
                       class="inline-flex items-center px-4 py-2 border border-teal-300 text-teal-700 rounded-lg hover:bg-teal-50">
                        <i class="fas fa-envelope mr-2"></i> Request Update
                    </a>
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-print mr-2"></i> Print Profile
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Associated Users Section (Optional) -->
    @if($facility->users->count() > 0)
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Associated Users</h3>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Primary Contact</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($facility->users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center">
                                            <span class="text-teal-600 font-medium">
                                                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->pivot->position ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->pivot->department ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($user->pivot->is_primary_contact)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Yes
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        No
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
    
    .bg-gradient-to-r {
        background: #0d9488 !important;
    }
}
</style>
@endpush