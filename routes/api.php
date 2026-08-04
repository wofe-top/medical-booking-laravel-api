<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;


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
    Route::post('/doctors', [DoctorController::class, 'store']);
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctor', [DoctorController::class, 'index']);
    Route::get('/doctors/{doctorProfile}/available-slots', [DoctorController::class, 'getAvailableSlots']);

    // Patients
    Route::get('/users', [UserController::class, 'index']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
});
