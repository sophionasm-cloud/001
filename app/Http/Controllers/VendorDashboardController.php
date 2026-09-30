<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Vendor');
    }

    public function index()
    {
        $vendor = auth()->user()->vendor;
        if (!$vendor) {
            return redirect('vendor/register');
        }
        if ($vendor->approval_status !== 'active') {
            return view('vendor.pending', compact('vendor'));
        }

        $totalSales = $vendor->products()->with('orderItems')
            ->get()->sum(fn($p) => $p->orderItems->sum('quantity'));
        $totalRevenue = $vendor->products()->with('orderItems')
            ->get()->sum(fn($p) => $p->orderItems->sum(fn($oi) => $oi->price * $oi->quantity));
        $recentOrders = $vendor->products()->with('orderItems.order')->latest()->take(5)->get();
        $lowStockProducts = $vendor->products()->where('stock', '<', 10)->get();

        return view('vendor.dashboard', compact('vendor', 'totalSales', 'totalRevenue', 'recentOrders', 'lowStockProducts'));
    }
}