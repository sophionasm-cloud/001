<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(CartService $cart)
    {
        $cartItems = $cart->content();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'total' => $cart->total(),
        ]);
    }

    public function store(Request $request, CartService $cart)
    {
        if ($cart->count() === 0) {
            return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'address_line1' => 'nullable|string',
            'city' => 'nullable|string',
            'zip_code' => 'nullable|string',
        ]);

        $user = auth()->user();
        $fullAddress = trim(($validated['address_line1'] ?? '') . ', ' . ($validated['city'] ?? '') . ', ' . ($validated['zip_code'] ?? ''));

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user ? $user->id : 1,
                'subtotal' => $cart->total(),
                'shipping_cost' => 0.00,
                'tax' => 0.00,
                'discount' => 0.00,
                'total' => $cart->total(),
                'status' => 'pending',
                'shipping_address' => $fullAddress ?: 'Default Address',
                'shipping_method' => 'Standard',
            ]);

            foreach ($cart->content() as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'vendor_id' => $item['options']['vendor_id'] ?? null,
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                ]);
            }

            $cart->destroy();
            DB::commit();

            return redirect()->route('home')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }
}
