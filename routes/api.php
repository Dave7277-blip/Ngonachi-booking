<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PackageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Lumière Studios
|--------------------------------------------------------------------------
|
| Public routes:   no authentication required
| Admin routes:    require a valid Sanctum token (auth:sanctum middleware)
|
| Install Sanctum:
|   composer require laravel/sanctum
|   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
|   php artisan migrate
|
| Add to config/auth.php guards:
|   'api' => ['driver' => 'sanctum', 'provider' => 'users'],
|
*/

// ── PUBLIC ROUTES ─────────────────────────────────────────────────────────

// Authentication
Route::post('/auth/login',          [AuthController::class, 'login']);
Route::post('/auth/forgot-password',[PasswordResetController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [PasswordResetController::class, 'resetPassword']);

// Booking submission (clients)
Route::post('/bookings', [BookingController::class, 'store']);

// Contact form submission (clients)
Route::post('/contacts', [ContactController::class, 'store']);

// Public package listing
Route::get('/packages', [PackageController::class, 'index']);

// Public gallery listing
Route::get('/gallery', [GalleryController::class, 'index']);


// ── ADMIN ROUTES (auth:sanctum) ────────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Dashboard summary
    Route::get('/dashboard', [BookingController::class, 'dashboard']);

    // Bookings management
    Route::get('/bookings',                        [BookingController::class, 'index']);
    Route::get('/bookings/{booking}',              [BookingController::class, 'show']);
    Route::patch('/bookings/{booking}/status',     [BookingController::class, 'updateStatus']);
    Route::delete('/bookings/{booking}',           [BookingController::class, 'destroy']);

    // Contacts / messages management
    Route::get('/contacts',                        [ContactController::class, 'index']);
    Route::patch('/contacts/{contact}/read',       [ContactController::class, 'markRead']);
    Route::delete('/contacts/{contact}',           [ContactController::class, 'destroy']);

    // Package CRUD (index is public, the rest are admin-only)
    Route::post('/packages',              [PackageController::class, 'store']);
    Route::get('/packages/{package}',    [PackageController::class, 'show']);
    Route::put('/packages/{package}',    [PackageController::class, 'update']);
    Route::delete('/packages/{package}', [PackageController::class, 'destroy']);

    // Gallery management
    Route::post('/gallery',                        [GalleryController::class, 'store']);
    Route::delete('/gallery/{galleryImage}',       [GalleryController::class, 'destroy']);
});