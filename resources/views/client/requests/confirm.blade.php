@extends('layouts.client')

@section('title', 'Confirm Delivery')
@section('page-title', 'Confirm Delivery Receipt')

@section('content')
<div class="max-w-2xl mx-auto">
    
    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-start gap-3 p-4 mb-4 bg-green-50 border border-green-200 rounded-xl">
        <i class="fas fa-check-circle text-green-500 flex-shrink-0 mt-0.5"></i>
        <span class="text-green-800 font-medium">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="flex items-start gap-3 p-4 mb-4 bg-red-50 border border-red-200 rounded-xl">
        <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0 mt-0.5"></i>
        <span class="text-red-800 font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.requests.show', $request) }}" class="text-gray-400 hover:text-teal-600 transition-colors">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Confirm Delivery</h2>
                <p class="text-sm text-gray-500">Request #{{ $request->request_number }}</p>
            </div>
        </div>
    </div>

    {{-- Main Confirmation Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-teal-500 to-green-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-white">Confirm Receipt</h3>
                    <p class="text-teal-100 text-sm">Complete the delivery confirmation</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('client.requests.confirm.submit', $request) }}" method="POST" class="p-6" id="confirmForm">
            @csrf
            
            {{-- Delivery Info --}}
            <div class="bg-teal-50 border border-teal-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-truck text-teal-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-teal-800">Ready for confirmation</p>
                        @if($request->delivered_at)
                        <p class="text-sm text-teal-600">
                            <i class="far fa-clock mr-1"></i>
                            Delivered: {{ $request->delivered_at->format('M d, Y h:i A') }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Recipient Name --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="recipient_name" 
                       value="{{ old('recipient_name', auth()->user()->first_name . ' ' . auth()->user()->last_name) }}"
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                       placeholder="Enter your name">
                @error('recipient_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes (Optional) --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Notes (Optional)
                </label>
                <textarea name="notes" 
                          rows="3"
                          class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                          placeholder="Any comments about the delivery?">{{ old('notes') }}</textarea>
            </div>

            {{-- Request Summary --}}
            <div class="bg-gray-50 rounded-lg p-5 mb-6">
                <h4 class="font-medium text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-receipt text-teal-500 text-sm"></i>
                    Request Summary
                </h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Specimen:</span>
                        <span class="font-medium text-gray-800">{{ ucfirst($request->specimen_type) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Priority:</span>
                        <span class="font-medium text-gray-800">{{ ucfirst($request->priority_level) }}</span>
                    </div>
                    <div class="border-t border-gray-200 my-2"></div>
                    <div>
                        <span class="text-gray-500 block mb-1">Pickup:</span>
                        <span class="font-medium text-gray-800 text-sm">{{ $request->pickup_address }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">Delivery:</span>
                        <span class="font-medium text-gray-800 text-sm">{{ $request->delivery_address }}</span>
                    </div>
                </div>
            </div>

            {{-- Courier Info (if assigned) --}}
            @if($request->courier)
            <div class="border border-gray-200 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-user text-teal-500 text-sm"></i>
                    Delivered By
                </h4>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($request->courier->name) }}&background=00B8A9&color=fff&size=40" 
                         alt="{{ $request->courier->name }}"
                         class="w-10 h-10 rounded-full">
                    <div>
                        <p class="font-medium text-gray-800">{{ $request->courier->name }}</p>
                        <p class="text-xs text-gray-500">{{ $request->courier->phone }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex gap-3">
                <a href="{{ route('client.requests.show', $request) }}" 
                   class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition text-sm text-center">
                    Cancel
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="flex-1 px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold transition text-sm">
                    Confirm Receipt
                </button>
            </div>
        </form>
    </div>

    {{-- Link to view proofs --}}
    <div class="mt-5 text-center">
        <a href="{{ route('client.requests.proofs', $request) }}" class="text-sm text-teal-600 hover:text-teal-700">
            <i class="fas fa-images mr-1"></i> View Delivery Proofs
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Confirming...';
        document.getElementById('confirmForm').submit();
    });
</script>
@endpush