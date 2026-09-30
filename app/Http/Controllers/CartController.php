<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = auth()->user()->cart ?? new Cart();
        $items = $cart->items()->with('product')->get();
        $total = $items->sum(fn($item) => $item->product->selling_price * $item->quantity);
        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Not enough stock');
        }
        $cart = auth()->user()->cart ?? Cart::create(['user_id' => auth()->id()]);
        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        if ($cartItem) {
            $cartItem->update(['quantity' => $cartItem->quantity + $request->quantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }
        return back()->with('success', 'Product added to cart');
    }

    public function update(Request $request, $itemId)
    {
        $item = auth()->user()->cart->items()->findOrFail($itemId);
        $item->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Cart updated');
    }

    public function remove($itemId)
    {
        auth()->user()->cart->items()->findOrFail($itemId)->delete();
        return back()->with('success', 'Item removed from cart');
    }
}