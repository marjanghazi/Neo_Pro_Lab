<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\FormsController;

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

// Pickup Routes
Route::get('/schedule-pickup', [PickupController::class, 'create'])->name('pickup.create');
Route::post('/schedule-pickup', [PickupController::class, 'store'])->name('pickup.store');

// Forms Download
Route::get('/download/{filename}', [FormsController::class, 'download'])->name('download');