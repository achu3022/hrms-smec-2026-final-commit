<?php

use App\Http\Controllers\EmployeeFormController;
use App\Http\Controllers\ProfileController;

Route::redirect('/', '/login');
Route::get('/registration', [EmployeeFormController::class, 'create'])->name('employee.form');
Route::post('/submit', [EmployeeFormController::class, 'store'])->name('employee.store');

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;

Route::middleware(['auth', 'role:Super Admin|HR Manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/status', [EmployeeController::class, 'updateStatus'])->name('employees.status');
    Route::get('employees/{employee}/id-card', [EmployeeController::class, 'idCard'])->name('employees.id-card');
    Route::get('employees/{employee}/experience-certificate', [EmployeeController::class, 'experienceCertificate'])->name('employees.experience-certificate');

    Route::resource('leave-types', \App\Http\Controllers\Admin\LeaveTypeController::class)->except(['create', 'show', 'edit']);
    Route::resource('leave-requests', \App\Http\Controllers\Admin\LeaveRequestController::class)->except(['create', 'show', 'edit', 'update']);
    Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class)->except(['create', 'show', 'edit', 'update']);
    Route::resource('assets', \App\Http\Controllers\Admin\AssetController::class)->except(['create', 'show', 'edit']);
    Route::post('assets/{asset}/assign', [\App\Http\Controllers\Admin\AssetController::class, 'assign'])->name('assets.assign');
    Route::post('assets/return/{assignment}', [\App\Http\Controllers\Admin\AssetController::class, 'returnAsset'])->name('assets.return');

    Route::resource('api-tokens', \App\Http\Controllers\Admin\ApiTokenController::class)->only(['index', 'store', 'destroy']);

    Route::get('/organization/{type}', [\App\Http\Controllers\Admin\OrganizationController::class, 'index'])->name('organization.index');
    Route::post('/organization/{type}', [\App\Http\Controllers\Admin\OrganizationController::class, 'store'])->name('organization.store');
    Route::put('/organization/{type}/{id}', [\App\Http\Controllers\Admin\OrganizationController::class, 'update'])->name('organization.update');
    Route::delete('/organization/{type}/{id}', [\App\Http\Controllers\Admin\OrganizationController::class, 'destroy'])->name('organization.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
