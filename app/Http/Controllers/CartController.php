<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController
{
    public function view(CartService $cart)
    {
        return view('cart.index', [
            'items' => $cart->content(),
            'total' => $cart->total(),
        ]);
    }

    public function add(Product $product, Request $request, CartService $cart)
    {
        $quantity = (int) $request->input('quantity', 1);
        $cart->add($product, $quantity);

        return redirect()->route('cart.view')
            ->with('success', 'Product added to cart');
    }

    public function update(Request $request, CartService $cart)
    {
        if ($request->has('quantities') && is_array($request->quantities)) {
            foreach ($request->quantities as $rowId => $qty) {
                $cart->update($rowId, (int) $qty);
            }
        }

        return redirect()->route('cart.view')
            ->with('success', 'Cart updated');
    }

    public function remove(Request $request, CartService $cart)
    {
        $cart->remove($request->input('item_id'));

        return redirect()->route('cart.view')
            ->with('success', 'Item removed');
    }
}
