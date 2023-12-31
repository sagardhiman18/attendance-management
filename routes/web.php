<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/biometric-today', [AttendanceController::class, 'biometricToday'])->name('attendance.today');
Route::post('/biometric-withoutsandwich', [AttendanceController::class, 'biometricWwithoutsandwich'])->name('attendance.withoutsandwich');

// Route::post('/applyLeave', [AttendanceController::class, 'applyLeave'])->name('applyLeave.save');
