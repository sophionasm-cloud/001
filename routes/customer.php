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

    Route::get('/orders', function () {
        $user = auth()->user();
        $query = $user->orders()->latest();
        if (request('status') && request('status') !== 'all') {
            $query->where('status', request('status'));
        }
        $orders = $query->paginate(10);
        return view('orders.index', compact('orders'));
    })->name('orders.index');
});
