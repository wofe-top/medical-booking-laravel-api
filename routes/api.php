<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;


// Roues Auth Public


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Auth & Test
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    // Doctor
    Route::get('/doctor', [DoctorController::class, 'index']);
    Route::get('/doctors/{doctorProfile}/available-slots', [DoctorController::class, 'getAvailableSlots']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
});
