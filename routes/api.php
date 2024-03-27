<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('attendances', AttendanceController::class)
        ->except(['destroy', 'update']);
});

Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
});
