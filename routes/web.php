<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/attendances/{user}/report', [AttendanceController::class, 'report'])
    ->middleware('auth:admin_panel')
    ->name('report');
