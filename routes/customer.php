<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('auth.customer-login');
})->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $orders = $user->orders()->latest()->take(5)->get();
        $cart = $user->cart;
        return view('customer.dashboard', compact('user', 'orders', 'cart'));
    })->name('dashboard');
});
