<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class VendorOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Vendor');
    }

    public function index()
    {
        $vendor = auth()->user()->vendor;
        $orderItems = OrderItem::where('vendor_id', $vendor->id)
            ->with('order', 'product')
            ->latest()
            ->paginate(10);
        return view('vendor.orders.index', compact('orderItems'));
    }

    public function show($orderId)
    {
        $vendor = auth()->user()->vendor;
        $order = OrderItem::where('vendor_id', $vendor->id)
            ->where('id', $orderId)
            ->with('order', 'product')
            ->firstOrFail();
        return view('vendor.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $vendor = auth()->user()->vendor;
        $order = OrderItem::where('vendor_id', $vendor->id)->findOrFail($orderId);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Order status updated');
    }
}