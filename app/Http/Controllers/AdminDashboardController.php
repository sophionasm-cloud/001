<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
    }

    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');
        $totalVendors = Vendor::where('approval_status', 'active')->count();
        $totalCustomers = User::whereHas('role', fn($q) => $q->where('name', 'Customer'))->count();
        $pendingVendors = Vendor::where('approval_status', 'pending')->count();
        return view('admin.dashboard', compact('totalOrders', 'totalRevenue', 'totalVendors', 'totalCustomers', 'pendingVendors'));
    }
}