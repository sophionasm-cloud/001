<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected function getCart(): array
    {
        return Session::get('cart', []);
    }

    public function content()
    {
        return collect($this->getCart());
    }

    public function add(Product $product, int $quantity = 1)
    {
        $cart = $this->getCart();
        $id = $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $quantity;
        } else {
            $cart[$id] = [
                'rowId' => $id,
                'id' => $product->id,
                'name' => $product->name,
                'qty' => $quantity,
                'price' => (float) ($product->selling_price ?? $product->price ?? 0),
                'options' => [
                    'vendor_id' => $product->vendor_id ?? null,
                ],
            ];
        }

        Session::put('cart', $cart);
    }

    public function update($rowId, int $qty)
    {
        $cart = $this->getCart();

        if (isset($cart[$rowId])) {
            if ($qty <= 0) {
                unset($cart[$rowId]);
            } else {
                $cart[$rowId]['qty'] = $qty;
            }
            Session::put('cart', $cart);
        }
    }

    public function remove($rowId)
    {
        $cart = $this->getCart();
        unset($cart[$rowId]);
        Session::put('cart', $cart);
    }

    public function destroy()
    {
        Session::forget('cart');
    }

    public function total(): float
    {
        return collect($this->getCart())->sum(fn($item) => $item['price'] * $item['qty']);
    }

    public function count(): int
    {
        return collect($this->getCart())->sum('qty');
    }
}
