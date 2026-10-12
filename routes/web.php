<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// Public storefront
Route::get('/', fn() => view('welcome'))->name('home');

// Products (public)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Cart
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Auth routes (login / register / logout)
Route::get('/login', fn() => view('auth.customer-login'))->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');

// Shortcuts that views link to (previously 404)
Route::middleware('auth')->group(function () {
    Route::redirect('/home', '/customer/dashboard');
    Route::redirect('/orders', '/customer/orders');
    Route::redirect('/profile', '/customer/dashboard')->name('profile.show');
});
Route::redirect('/wishlist', '/products');
Route::get('/orders/{order}', function (\App\Models\Order $order) {
    abort_unless($order->user_id === auth()->id(), 404);
    $order->load('items.product');
    return view('orders.show', compact('order'));
})->name('orders.show');

Route::get('/health-check', function () {
    $results = [];
    $paths = [
        'storage' => storage_path(),
        'sessions' => storage_path('framework/sessions'),
        'views' => storage_path('framework/views'),
        'cache' => storage_path('framework/cache/data'),
        'database_dir' => database_path(),
        'database_file' => database_path('database.sqlite'),
    ];
    foreach ($paths as $name => $path) {
        $results['paths'][$name] = [
            'exists' => file_exists($path),
            'writable' => is_writable($path),
        ];
    }

    try {
        $results['db_connection'] = config('database.default');
        $results['users_count'] = \App\Models\User::count();
        $results['roles_count'] = \App\Models\Role::count();
        $results['users'] = \App\Models\User::with('role')->get(['id', 'email', 'name', 'role_id']);
    } catch (\Throwable $e) {
        $results['db_error'] = $e->getMessage();
    }

    try {
        session(['health_test' => 'ok']);
        session()->save();
        $results['session_write'] = 'OK';
    } catch (\Throwable $e) {
        $results['session_error'] = $e->getMessage();
    }

    try {
        cache()->put('health_test', 'ok', 10);
        $results['cache_write'] = 'OK';
    } catch (\Throwable $e) {
        $results['cache_error'] = $e->getMessage();
    }

    return response()->json($results);
});
