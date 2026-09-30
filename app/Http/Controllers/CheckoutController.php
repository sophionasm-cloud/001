<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return redirect('cart')->with('error', 'Cart is empty');
        }
        $items = $cart->items()->with('product')->get();
        $subtotal = $items->sum(fn($item) => $item->product->selling_price * $item->quantity);
        return view('checkout.index', compact('items', 'subtotal'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $cart = auth()->user()->cart;
        $items = $cart->items()->with('product')->get();
        $subtotal = $items->sum(fn($item) => $item->product->selling_price * $item->quantity);
        $shipping_cost = 10;
        $tax = $subtotal * 0.1;
        $discount = 0;
        $total = $subtotal + $shipping_cost + $tax - $discount;

        foreach ($items as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', 'Insufficient stock for ' . $item->product->name);
            }
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping_cost,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
                'shipping_method' => $validated['shipping_method'],
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'vendor_id' => $item->product->vendor_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->selling_price,
                ]);
                $item->product->decrement('stock', $item->quantity);
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
            ]);

            $cart->items()->delete();
            DB::commit();
            return redirect('order/' . $order->id)->with('success', 'Order placed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing order: ' . $e->getMessage());
        }
    }

    public function orderConfirmation($orderId)
    {
        $order = Order::with('items.product.vendor', 'payment')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);
        return view('checkout.confirmation', compact('order'));
    }
}