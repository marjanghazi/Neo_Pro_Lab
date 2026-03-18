@extends('layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Settings</span>
    </div>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Left Column - Settings Navigation -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-teal-800 p-4">
                <h3 class="text-lg font-bold text-white">Settings</h3>
                <p class="text-teal-100 text-sm">Configure system preferences</p>
            </div>
            <nav class="space-y-1 p-4">
                <a href="{{ route('admin.settings.general') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings.general') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-cog w-5"></i>
                    <span>General Settings</span>
                </a>
                <a href="{{ route('admin.settings.notifications') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings.notifications') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-bell w-5"></i>
                    <span>Notifications</span>
                </a>
                <a href="{{ route('admin.settings.courier') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings.courier') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-shipping-fast w-5"></i>
                    <span>Courier Settings</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-file-invoice w-5"></i>
                    <span>Billing & Payments</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-shield-alt w-5"></i>
                    <span>Security</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-database w-5"></i>
                    <span>Database</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-plug w-5"></i>
                    <span>Integrations</span>
                </a>
            </nav>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <h3 class="text-lg font-bold mb-4">System Status</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Database</span>
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Online
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Storage</span>
                    <span class="text-sm font-medium">65% used</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Last Backup</span>
                    <span class="text-sm">24 hours ago</span>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <button class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                        <i class="fas fa-download mr-2"></i> Backup Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Settings Content -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">General Settings</h2>
                <p class="text-gray-600">Configure basic system settings and preferences</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- System Information Card -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-2xl text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-blue-800">System Information</h3>
                            <p class="text-sm text-blue-600">Version: 2.1.0</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">NeoProLab Courier Management System</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">PHP Version:</span>
                            <span class="font-medium">{{ phpversion() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Laravel Version:</span>
                            <span class="font-medium">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Environment:</span>
                            <span class="font-medium">{{ app()->environment() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-chart-bar text-2xl text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-green-800">System Statistics</h3>
                            <p class="text-sm text-green-600">Current month</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Requests:</span>
                            <span class="font-bold text-green-800">{{ \App\Models\SpecimenRequest::count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Active Couriers:</span>
                            <span class="font-bold text-green-800">
                                {{ \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'courier'))->where('is_active', true)->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Registered Facilities:</span>
                            <span class="font-bold text-green-800">{{ \App\Models\Facility::count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Actions -->
                <div class="md:col-span-2 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h3 class="font-bold text-yellow-800 mb-4">Maintenance Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button class="px-4 py-2 bg-white border border-yellow-300 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors text-sm font-medium">
                            <i class="fas fa-redo mr-2"></i> Clear Cache
                        </button>
                        <button class="px-4 py-2 bg-white border border-yellow-300 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors text-sm font-medium">
                            <i class="fas fa-database mr-2"></i> Optimize DB
                        </button>
                        <button class="px-4 py-2 bg-white border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors text-sm font-medium">
                            <i class="fas fa-trash-alt mr-2"></i> Clear Logs
                        </button>
                    </div>
                </div>

                <!-- Quick Settings -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-bold mb-4">Quick Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium">Maintenance Mode</p>
                                <p class="text-sm text-gray-500">Take system offline</p>
                            </div>
                            <label class="settings-switch">
                                <input type="checkbox">
                                <span class="settings-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium">Auto Updates</p>
                                <p class="text-sm text-gray-500">Automatic system updates</p>
                            </div>
                            <label class="settings-switch">
                                <input type="checkbox" checked>
                                <span class="settings-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium">Debug Mode</p>
                                <p class="text-sm text-gray-500">Show debug information</p>
                            </div>
                            <label class="settings-switch">
                                <input type="checkbox">
                                <span class="settings-slider"></span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium">Email Notifications</p>
                                <p class="text-sm text-gray-500">Send email alerts</p>
                            </div>
                            <label class="settings-switch">
                                <input type="checkbox" checked>
                                <span class="settings-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Toggle Switch Styles - Scoped to settings page */
.settings-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.settings-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.settings-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.settings-slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .settings-slider {
    background-color: #00B8A9;
}

input:checked + .settings-slider:before {
    transform: translateX(26px);
}

/* Card styles - Override any conflicting styles */
.bg-white.rounded-lg.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Ensure proper spacing */
.space-y-1 > * + * {
    margin-top: 0.25rem;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid {
        gap: 1rem;
    }
}
</style>
@endsection