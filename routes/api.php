<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->middleware('guest')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});


Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
});
