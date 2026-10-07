<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\VendorProductController;
use App\Http\Controllers\VendorOrderController;

Route::get('/login', function () {
    return view('auth.vendor-login');
})->name('login');

Route::get('/register', function () {
    return view('vendor.register');
})->name('register');

Route::post('/register', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'store_name'  => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    \App\Models\Vendor::create([
        'user_id'         => auth()->id(),
        'store_name'      => $validated['store_name'],
        'description'     => $validated['description'] ?? null,
        'approval_status' => 'pending',
    ]);

    return redirect()->route('vendor.dashboard')->with('success', 'Vendor account created! Waiting for approval.');
})->middleware('auth')->name('register.submit');

Route::middleware(['auth', 'role:Vendor'])->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', VendorProductController::class);
    Route::get('/orders', [VendorOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [VendorOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [VendorOrderController::class, 'updateStatus'])->name('orders.updateStatus');
});
