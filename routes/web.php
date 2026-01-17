<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AdminFacilityController;
use App\Http\Controllers\Admin\AdminCourierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\FormsController;

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
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {

            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
            Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

            // Users management
            Route::resource('users', AdminUserController::class);
            Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])
                ->name('users.toggle-status');

            // Requests management
            Route::get('requests', [AdminRequestController::class, 'index'])->name('requests.index');
            Route::get('requests/{request}', [AdminRequestController::class, 'show'])->name('requests.show');
            Route::post('requests/{request}/assign', [AdminRequestController::class, 'assignCourier'])->name('requests.assign');
            Route::post('requests/{request}/status', [AdminRequestController::class, 'updateStatus'])->name('requests.status');

            // Couriers management
            Route::get('couriers', [AdminCourierController::class, 'index'])->name('couriers.index');
            Route::get('couriers/create', [AdminCourierController::class, 'create'])->name('couriers.create');
            Route::post('couriers', [AdminCourierController::class, 'store'])->name('couriers.store');
            Route::get('couriers/{courier}', [AdminCourierController::class, 'show'])->name('couriers.show');
            Route::get('couriers/{courier}/edit', [AdminCourierController::class, 'edit'])->name('couriers.edit');
            Route::put('couriers/{courier}', [AdminCourierController::class, 'update'])->name('couriers.update');
            Route::post('couriers/{courier}/deactivate', [AdminCourierController::class, 'deactivate'])->name('couriers.deactivate');
            Route::delete('couriers/{courier}', [AdminCourierController::class, 'destroy'])->name('couriers.destroy');

            // Facilities management
            Route::get('facilities', [AdminFacilityController::class, 'index'])->name('facilities.index');
            Route::get('facilities/create', [AdminFacilityController::class, 'create'])->name('facilities.create');
            Route::post('facilities', [AdminFacilityController::class, 'store'])->name('facilities.store');
            Route::get('facilities/{facility}', [AdminFacilityController::class, 'show'])->name('facilities.show');
            Route::get('facilities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('facilities.edit');
            Route::put('facilities/{facility}', [AdminFacilityController::class, 'update'])->name('facilities.update');
            Route::post('facilities/{facility}/approve', [AdminFacilityController::class, 'approve'])->name('facilities.approve');
            Route::post('facilities/{facility}/reject', [AdminFacilityController::class, 'reject'])->name('facilities.reject');
            Route::delete('facilities/{facility}', [AdminFacilityController::class, 'destroy'])->name('facilities.destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Client Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('client')
        ->name('client.')
        ->middleware('role:client')
        ->group(function () {
            Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
            Route::get('/requests', [ClientController::class, 'requests'])->name('requests.index');
            Route::get('/requests/create', [ClientController::class, 'createRequest'])->name('requests.create');
            Route::post('/requests', [ClientController::class, 'storeRequest'])->name('requests.store');
            Route::get('/requests/{request}/track', [ClientController::class, 'trackRequest'])->name('requests.track');
        });

    /*
    |--------------------------------------------------------------------------
    | Courier Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('courier')
        ->name('courier.')
        ->middleware('role:courier')
        ->group(function () {
            Route::get('/dashboard', [CourierController::class, 'dashboard'])->name('dashboard');
            Route::get('/assignments', [CourierController::class, 'assignments'])->name('assignments.index');
            Route::post('/assignments/{request}/accept', [CourierController::class, 'acceptAssignment'])->name('assignments.accept');
            Route::post('/location', [CourierController::class, 'updateLocation'])->name('location.update');
        });
});
