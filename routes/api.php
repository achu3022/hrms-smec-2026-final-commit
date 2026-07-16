<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/employees', [\App\Http\Controllers\Api\EmployeeApiController::class, 'index']);
    Route::post('/attendance', [\App\Http\Controllers\Api\AttendanceApiController::class, 'store']);
});
