<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\PickleballBookingController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\RoomBookingController;
use App\Http\Controllers\Api\FunctionHallController;
use App\Http\Controllers\Api\HallBookingController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/resend', [AuthController::class, 'resendVerification'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');

// Public resource listing routes (no auth required)
Route::get('/courts', [CourtController::class, 'index']);
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/function-halls', [FunctionHallController::class, 'index']);

// Public booking data for availability display
Route::get('/pickleball-bookings/all', [PickleballBookingController::class, 'all']);

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Pickleball Courts
    Route::get('/courts/{court}', [CourtController::class, 'show']);
    Route::get('/courts/{court}/availability', [CourtController::class, 'availability']);

    // Pickleball Bookings
    Route::get('/pickleball-bookings', [PickleballBookingController::class, 'index']);
    Route::post('/pickleball-bookings', [PickleballBookingController::class, 'store']);
    Route::get('/pickleball-bookings/{pickleballBooking}', [PickleballBookingController::class, 'show']);
    Route::put('/pickleball-bookings/{pickleballBooking}', [PickleballBookingController::class, 'update']);
    Route::delete('/pickleball-bookings/{pickleballBooking}', [PickleballBookingController::class, 'destroy']);

    // Hotel Rooms
    Route::get('/rooms/{room}', [RoomController::class, 'show']);
    Route::get('/rooms/{room}/availability', [RoomController::class, 'availability']);

    // Room Bookings
    Route::get('/room-bookings', [RoomBookingController::class, 'index']);
    Route::post('/room-bookings', [RoomBookingController::class, 'store']);
    Route::get('/room-bookings/{roomBooking}', [RoomBookingController::class, 'show']);
    Route::put('/room-bookings/{roomBooking}', [RoomBookingController::class, 'update']);
    Route::delete('/room-bookings/{roomBooking}', [RoomBookingController::class, 'destroy']);

    // Function Halls
    Route::get('/function-halls/{functionHall}', [FunctionHallController::class, 'show']);
    Route::get('/function-halls/{functionHall}/availability', [FunctionHallController::class, 'availability']);

    // Hall Bookings
    Route::get('/hall-bookings', [HallBookingController::class, 'index']);
    Route::post('/hall-bookings', [HallBookingController::class, 'store']);
    Route::get('/hall-bookings/{hallBooking}', [HallBookingController::class, 'show']);
    Route::put('/hall-bookings/{hallBooking}', [HallBookingController::class, 'update']);
    Route::delete('/hall-bookings/{hallBooking}', [HallBookingController::class, 'destroy']);

    // ========================================
    // ADMIN ROUTES (require admin role)
    // ========================================
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard']);

        // Users management
        Route::get('/users', [AdminController::class, 'users']);

        // Courts CRUD
        Route::post('/courts', [CourtController::class, 'store']);
        Route::put('/courts/{court}', [CourtController::class, 'update']);
        Route::delete('/courts/{court}', [CourtController::class, 'destroy']);

        // Rooms CRUD
        Route::post('/rooms', [RoomController::class, 'store']);
        Route::put('/rooms/{room}', [RoomController::class, 'update']);
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

        // Function Halls CRUD
        Route::post('/function-halls', [FunctionHallController::class, 'store']);
        Route::put('/function-halls/{functionHall}', [FunctionHallController::class, 'update']);
        Route::delete('/function-halls/{functionHall}', [FunctionHallController::class, 'destroy']);

        // All Bookings (admin view with all users)
        Route::get('/pickleball-bookings', [AdminController::class, 'pickleballBookings']);
        Route::get('/room-bookings', [AdminController::class, 'roomBookings']);
        Route::get('/hall-bookings', [AdminController::class, 'hallBookings']);

        // Update booking statuses
        Route::patch('/pickleball-bookings/{booking}/status', [AdminController::class, 'updatePickleballBooking']);
        Route::patch('/room-bookings/{booking}/status', [AdminController::class, 'updateRoomBooking']);
        Route::patch('/hall-bookings/{booking}/status', [AdminController::class, 'updateHallBooking']);

        // New Booking Management Routes
        Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirm']);
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
        Route::put('/bookings/{id}/move', [BookingController::class, 'move']);

        // Hall Booking Management
        Route::post('/hall-bookings/{id}/confirm', [HallBookingController::class, 'confirm']);
        Route::post('/hall-bookings/{id}/cancel', [HallBookingController::class, 'cancel']);
    });
});

