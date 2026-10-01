public function store(Request $request, CartService $cart)
{
    if ($cart->count() === 0) {
        return redirect()->route('cart.view')->with('error', 'Your cart is empty.');
    }

    $validated = $request->validate([
        'email' => 'required|email',
        'phone' => 'required|string',
        'address' => 'required|string',
        'city' => 'required|string',
        'zip' => 'required|string',
    ]);

    $user = auth()->user();
    $fullAddress = "{$validated['address']}, {$validated['city']}, {$validated['zip']} | Phone: {$validated['phone']}";

    DB::beginTransaction();
    try {
        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => $cart->total(),
            'shipping_cost' => 0.00,
            'tax' => 0.00,
            'discount' => 0.00,
            'total' => $cart->total(),
            'status' => 'pending',
            'shipping_address' => $fullAddress,
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
