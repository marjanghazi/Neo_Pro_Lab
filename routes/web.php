<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminReportsController;
use App\Http\Controllers\Admin\AdminFacilityController;
use App\Http\Controllers\Admin\AdminCourierController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Courier\CourierController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\Client\DocumentController;
use App\Http\Controllers\Client\FacilityController;

/*
|--------------------------------------------------------------------------
| Public Website Pages
|--------------------------------------------------------------------------
*/

Route::get('/', [PagesController::class, 'home'])->name('home');
Route::get('/about', [PagesController::class, 'about'])->name('about');
Route::get('/services', [PagesController::class, 'services'])->name('services');
Route::get('/coverage', [PagesController::class, 'coverage'])->name('coverage');
Route::get('/specimen-handling', [PagesController::class, 'specimenHandling'])->name('specimen-handling');
Route::get('/pricing', [PagesController::class, 'pricing'])->name('pricing');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::get('/forms', [PagesController::class, 'forms'])->name('forms');
Route::get('/hipaa-notice', [PagesController::class, 'hipaaNotice'])->name('hipaa-notice');
Route::get('/insurance', [PagesController::class, 'insurance'])->name('insurance');
Route::get('/privacy', [PagesController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PagesController::class, 'terms'])->name('terms');

/*
|--------------------------------------------------------------------------
| Pickup & Forms (Public)
|--------------------------------------------------------------------------
*/
Route::get('/schedule-pickup', [PickupController::class, 'create'])->name('pickup.create');
Route::post('/schedule-pickup', [PickupController::class, 'store'])->name('pickup.store');
Route::get('/download/{filename}', [FormsController::class, 'download'])->name('download');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/requests/{request}/status', [AdminRequestController::class, 'updateStatus'])->name('requests.status');

        // Admin pricing routes
        Route::post('/requests/{request}/calculate-price', [AdminRequestController::class, 'calculatePrice'])->name('requests.calculate-price');
        Route::post('/requests/{request}/send-quote', [AdminRequestController::class, 'createQuote'])->name('requests.send-quote');
        Route::post('/requests/{request}/assign-with-quote', [AdminRequestController::class, 'assignWithQuote'])->name('requests.assign-with-quote');

        // Profile
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');

        // Settings
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/general', [AdminSettingsController::class, 'general'])->name('settings.general');
        Route::get('/settings/notifications', [AdminSettingsController::class, 'notifications'])->name('settings.notifications');
        Route::get('/settings/courier', [AdminSettingsController::class, 'courier'])->name('settings.courier');
        Route::get('/settings/payment', [AdminSettingsController::class, 'payment'])->name('settings.payment');
        Route::post('/settings/update', [AdminSettingsController::class, 'update'])->name('settings.update');

        // Reports
        Route::get('/reports', [AdminReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/performance', [AdminReportsController::class, 'performance'])->name('reports.performance');
        Route::get('/reports/requests', [AdminReportsController::class, 'requests'])->name('reports.requests');
        Route::get('/reports/facilities', [AdminReportsController::class, 'facilities'])->name('reports.facilities');
        Route::get('/reports/payments', [AdminReportsController::class, 'payments'])->name('reports.payments');
        Route::post('/reports/export', [AdminReportsController::class, 'export'])->name('reports.export');

        // Facilities
        Route::get('/facilities', [AdminFacilityController::class, 'index'])->name('facilities.index');
        Route::get('/facilities/create', [AdminFacilityController::class, 'create'])->name('facilities.create');
        Route::post('/facilities', [AdminFacilityController::class, 'store'])->name('facilities.store');
        Route::get('/facilities/{facility}', [AdminFacilityController::class, 'show'])->name('facilities.show');
        Route::get('/facilities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('facilities.edit');
        Route::put('/facilities/{facility}', [AdminFacilityController::class, 'update'])->name('facilities.update');
        Route::post('/facilities/{facility}/approve', [AdminFacilityController::class, 'approve'])->name('facilities.approve');
        Route::post('/facilities/{facility}/reject', [AdminFacilityController::class, 'reject'])->name('facilities.reject');
        Route::delete('/facilities/{facility}', [AdminFacilityController::class, 'destroy'])->name('facilities.destroy');

        // Couriers
        Route::get('/couriers', [AdminCourierController::class, 'index'])->name('couriers.index');
        Route::get('/couriers/create', [AdminCourierController::class, 'create'])->name('couriers.create');
        Route::post('/couriers', [AdminCourierController::class, 'store'])->name('couriers.store');
        Route::get('/couriers/{courier}', [AdminCourierController::class, 'show'])->name('couriers.show');
        Route::get('/couriers/{courier}/edit', [AdminCourierController::class, 'edit'])->name('couriers.edit');
        Route::put('/couriers/{courier}', [AdminCourierController::class, 'update'])->name('couriers.update');

        // Users - Place PENDING route BEFORE dynamic {user} routes to avoid conflict
        Route::get('/users/pending', [AdminUserController::class, 'pendingApprovals'])->name('users.pending');

        // Regular user routes
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

        // User approval routes
        Route::post('/users/{user}/approve', [AdminUserController::class, 'approveUser'])->name('users.approve');
        Route::delete('/users/{user}/reject', [AdminUserController::class, 'rejectUser'])->name('users.reject');

        // Requests
        Route::get('/requests', [AdminRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{request}', [AdminRequestController::class, 'show'])->name('requests.show');
        Route::post('/requests/{request}/assign', [AdminRequestController::class, 'assignCourier'])->name('requests.assign');
        Route::post('/requests/{request}/status', [AdminRequestController::class, 'updateStatus'])->name('requests.status');
        Route::post('/requests/{request}/update-payment', [AdminRequestController::class, 'updatePaymentStatus'])->name('requests.update-payment');

        // Payments (Admin)
        Route::get('/payments', [AdminRequestController::class, 'payments'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminRequestController::class, 'viewPayment'])->name('payments.show');
        Route::post('/payments/{payment}/refund', [AdminRequestController::class, 'refundPayment'])->name('payments.refund');
        Route::post('/payments/{payment}/mark-paid', [AdminRequestController::class, 'markPaymentAsPaid'])->name('payments.mark-paid');

        // Tracking
        Route::get('/tracking/{request}', [AdminRequestController::class, 'track'])->name('requests.track');
        Route::get('/api/courier/{courier}/location', [AdminRequestController::class, 'getCourierLocation'])->name('courier.location');
    });

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:client', 'user.approved'])->prefix('client')->name('client.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
    Route::post('/profile', [ClientController::class, 'updateProfile'])->name('profile.update');

    // Notifications
    Route::get('/notifications', [ClientController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{notification}/read', [ClientController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [ClientController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');

    // Requests
    Route::get('/requests', [ClientController::class, 'requests'])->name('requests.index');
    Route::get('/requests/create', [ClientController::class, 'createRequest'])->name('requests.create');

    // New Routes for Pricing & Preview
    Route::post('/requests/preview', [ClientController::class, 'previewRequest'])->name('requests.preview');
    Route::post('/requests/calculate-price', [ClientController::class, 'calculateRequestPrice'])->name('requests.calculate-price');

    Route::post('/requests', [ClientController::class, 'storeRequest'])->name('requests.store');
    Route::get('/requests/{request}', [ClientController::class, 'showRequest'])->name('requests.show');
    Route::get('/requests/{request}/track', [ClientController::class, 'trackRequest'])->name('requests.track');
    Route::post('/requests/{request}/cancel', [ClientController::class, 'cancelRequest'])->name('requests.cancel');
    Route::get('/requests/{request}/confirm', [ClientController::class, 'confirmDelivery'])->name('requests.confirm');
    Route::post('/requests/{request}/confirm', [ClientController::class, 'submitConfirmation'])->name('requests.confirm.submit');

    // Request Documents
    Route::get('/requests/{request}/documents', [ClientController::class, 'documents'])->name('requests.documents');
    Route::get('/documents/{document}/download', [ClientController::class, 'downloadDocument'])->name('documents.download');

    // Proofs
    Route::get('/requests/{request}/proofs', [ClientController::class, 'proofs'])->name('requests.proofs');

    // Tracking
    Route::get('/tracking', [ClientController::class, 'tracking'])->name('tracking');
    Route::get('/tracking/active', [ClientController::class, 'getActiveTracking'])->name('tracking.active');

    // API for real-time tracking
    Route::get('/api/tracking/{request}/courier-location', [ClientController::class, 'getCourierLocation'])->name('tracking.courier-location');
    Route::get('/api/tracking/{request}/details', [ClientController::class, 'getTrackingDetails'])->name('tracking.details');
    Route::get('/api/courier/{courier}/location', [ClientController::class, 'getCourierLocationApi'])->name('courier.location.api');

    // Reports
    Route::get('/reports', [ClientController::class, 'reports'])->name('reports');
    Route::post('/reports/download', [ClientController::class, 'downloadReport'])->name('reports.download');

    // Payment routes
    Route::get('/requests/{request}/payment', [ClientController::class, 'showPayment'])->name('payments.show');
    Route::post('/requests/{request}/payment/process', [ClientController::class, 'processPayment'])->name('payments.process');
    Route::get('/payments/{payment}/success', [ClientController::class, 'paymentSuccess'])->name('payments.success');
    Route::get('/payments/{payment}/callback', [ClientController::class, 'paymentCallback'])->name('payments.callback');
    Route::get('/payments/{payment}/receipt', [ClientController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::get('/payments', [ClientController::class, 'paymentHistory'])->name('payments.history');
    Route::get('/payments/{payment}', [ClientController::class, 'viewPayment'])->name('payments.view');

    // Document Upload Center Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/templates', [DocumentController::class, 'templates'])->name('templates');
        Route::get('/templates/{template}/download', [DocumentController::class, 'downloadTemplate'])->name('templates.download');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    });

      Route::get('/facility', [FacilityController::class, 'show'])
        ->name('facility.show');
});

/*
|--------------------------------------------------------------------------
| Courier Routes
|--------------------------------------------------------------------------
*/
Route::prefix('courier')
    ->name('courier.')
    ->middleware(['auth', 'role:courier', 'user.approved'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [CourierController::class, 'dashboard'])->name('dashboard');

        // Assignments
        Route::get('/assignments', [CourierController::class, 'assignments'])->name('assignments.index');
        Route::post('/assignments/{requestId}/accept', [CourierController::class, 'acceptAssignment'])->name('assignments.accept');

        // Location Tracking
        Route::post('/location', [CourierController::class, 'updateLocation'])->name('location.update');
        Route::get('/location/status', [CourierController::class, 'locationStatus'])->name('location.status');
        Route::post('/location/toggle', [CourierController::class, 'toggleLocation'])->name('location.toggle');

        // Requests Management
        Route::get('/requests', [CourierController::class, 'requests'])->name('requests.index');
        Route::get('/requests/{requestId}', [CourierController::class, 'viewRequest'])->name('requests.show');

        // Proof Workflow Routes - ADDED MISSING ROUTES
        Route::post('/requests/{requestId}/pickup-proof', [CourierController::class, 'submitPickupProof'])->name('requests.pickup-proof');
        Route::post('/requests/{requestId}/transit-proof', [CourierController::class, 'submitTransitProof'])->name('requests.transit-proof');
        Route::post('/requests/{requestId}/skip-proof', [CourierController::class, 'skipProofRequirement'])->name('requests.skip-proof');

        // Courier quote acceptance routes
        Route::post('/requests/{requestId}/accept-quote', [CourierController::class, 'acceptQuote'])->name('courier.requests.accept-quote');
        Route::post('/requests/{requestId}/decline-quote', [CourierController::class, 'declineQuote'])->name('courier.requests.decline-quote');
        Route::get('/requests/{requestId}/quote', [CourierController::class, 'viewQuote'])->name('courier.requests.quote');

        // ADD THE MISSING ARRIVAL PROOF ROUTE
        Route::post('/requests/{requestId}/arrival-proof', [CourierController::class, 'submitArrivalProof'])->name('requests.arrival-proof');

        // Delivery Workflow
        Route::post('/requests/{requestId}/start-pickup', [CourierController::class, 'startPickup'])->name('requests.start-pickup');
        Route::post('/requests/{requestId}/start-transit', [CourierController::class, 'startTransit'])->name('requests.start-transit');
        Route::post('/requests/{requestId}/arrive-destination', [CourierController::class, 'arriveAtDestination'])->name('requests.arrive-destination');
        Route::post('/requests/{requestId}/submit-delivery', [CourierController::class, 'submitDelivery'])->name('requests.submit-delivery');
        Route::post('/requests/{requestId}/complete', [CourierController::class, 'completeRequest'])->name('requests.complete');

        // Active Requests & Navigation
        Route::get('/active-request', [CourierController::class, 'getActiveRequest'])->name('active-request');
        Route::get('/active-pickups', [CourierController::class, 'activePickups'])->name('active-pickups');
        Route::get('/active-deliveries', [CourierController::class, 'activeDeliveries'])->name('active-deliveries');
        Route::get('/requests/{requestId}/navigation', [CourierController::class, 'getNavigation'])->name('requests.navigation');

        // History & Proofs
        Route::get('/history', [CourierController::class, 'history'])->name('history');
        Route::get('/proofs', [CourierController::class, 'proofs'])->name('proofs.index');
        Route::get('/proofs/{proof}', [CourierController::class, 'viewProof'])->name('proofs.show');

        // Profile
        Route::get('/profile', [CourierController::class, 'profile'])->name('profile');
        Route::post('/profile', [CourierController::class, 'updateProfile'])->name('profile.update');

        // Notifications
        Route::get('/notifications', [CourierController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/read', [CourierController::class, 'markNotificationAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [CourierController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');

        // API Endpoints for real-time updates
        Route::post('/api/cache-location', function (Request $request) {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'accuracy' => 'nullable|numeric',
                'speed' => 'nullable|numeric',
                'heading' => 'nullable|numeric',
                'altitude' => 'nullable|numeric',
                'battery_level' => 'nullable|numeric|min:0|max:100',
                'request_id' => 'nullable|exists:specimen_requests,id'
            ]);

            $locationData = [
                'latitude' => (float) $request->latitude,
                'longitude' => (float) $request->longitude,
                'accuracy' => $request->accuracy ? (float) $request->accuracy : 0,
                'speed' => $request->speed ? (float) $request->speed : 0,
                'heading' => $request->heading ? (float) $request->heading : 0,
                'altitude' => $request->altitude ? (float) $request->altitude : 0,
                'battery_level' => $request->battery_level,
                'timestamp' => now()->timestamp,
                'last_update' => now(),
                'courier_id' => auth()->id(),
                'courier_name' => auth()->user()->full_name,
                'is_online' => true,
                'request_id' => $request->request_id
            ];

            cache()->put('courier_location_' . auth()->id(), $locationData, 35);

            // Also store in database if CourierLocation model exists
            if (class_exists('App\Models\CourierLocation')) {
                try {
                    \App\Models\CourierLocation::updateOrCreate(
                        ['courier_id' => auth()->id()],
                        [
                            'latitude' => $request->latitude,
                            'longitude' => $request->longitude,
                            'accuracy' => $request->accuracy ?? 0,
                            'speed' => $request->speed ?? 0,
                            'heading' => $request->heading ?? 0,
                            'altitude' => $request->altitude ?? 0,
                            'battery_level' => $request->battery_level,
                            'is_online' => true,
                            'last_update' => now(),
                            'request_id' => $request->request_id
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to save courier location to database: ' . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'data' => $locationData]);
        })->name('api.cache-location');

        Route::get('/api/location/history/{requestId}', [CourierController::class, 'getLocationHistory'])->name('api.location.history');

        // API to get courier location for specific request
        Route::get('/api/requests/{requestId}/courier-location', [CourierController::class, 'getCourierLocationForRequest'])->name('api.requests.courier-location');
    });

/*
|--------------------------------------------------------------------------
| API Routes (Public for some endpoints)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // General API routes that require authentication
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return view('errors.404');
});
