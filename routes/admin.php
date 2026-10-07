<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminVendorController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminReportController;

Route::get('/login', function () {
    return view('auth.admin-login');
})->name('login');

Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/vendors/pending', [AdminVendorController::class, 'pending'])->name('vendors.pending');
    Route::post('/vendors/{vendor}/approve', [AdminVendorController::class, 'approve'])->name('vendors.approve');
    Route::post('/vendors/{vendor}/reject', [AdminVendorController::class, 'reject'])->name('vendors.reject');
    Route::post('/vendors/{vendor}/suspend', [AdminVendorController::class, 'suspend'])->name('vendors.suspend');
    Route::get('/vendors', [AdminVendorController::class, 'list'])->name('vendors.list');
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});
