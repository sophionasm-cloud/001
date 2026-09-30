<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
    }

    public function index()
    {
        $monthlySales = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total) as total')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->take(12)
        ->get();

        $topVendors = DB::table('vendors')
            ->join('order_items', 'vendors.id', '=', 'order_items.vendor_id')
            ->select('vendors.store_name', DB::raw('SUM(order_items.price * order_items.quantity) as total_sales'))
            ->groupBy('vendors.id', 'vendors.store_name')
            ->orderBy('total_sales', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact('monthlySales', 'topVendors'));
    }
}