<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Vendor;
use App\Models\Category;
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
        // 1. KPI Statistics from actual database
        $totalRevenue = (float) Order::sum('total');
        $totalOrders  = Order::count();
        $activeVendors = Vendor::where('approval_status', 'active')->count();
        $avgOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0.0;

        $stats = [
            'total_revenue'  => $totalRevenue,
            'total_orders'   => $totalOrders,
            'active_vendors' => $activeVendors,
            'avg_order'      => $avgOrderValue,
        ];

        // 2. 12-Month Sales & Orders Trend (Last 12 calendar months)
        $months = [];
        $revenueData = [];
        $ordersData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M');
            $months[] = $monthLabel;

            $monthOrders = Order::whereRaw("strftime('%Y-%m', created_at) = ?", [$monthKey])->get();
            $revenueData[] = round((float) $monthOrders->sum('total'), 2);
            $ordersData[]  = $monthOrders->count();
        }

        // 3. Sales by Category
        $categoryBreakdown = DB::table('categories')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('categories.name', DB::raw('COALESCE(SUM(order_items.price * order_items.quantity), 0) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        $catLabels = [];
        $catSeries = [];
        $catPcts = [];
        $totalCatSales = $categoryBreakdown->sum('total_sales');

        $colors = ['primary', 'success', 'warning', 'info', 'secondary'];
        $catList = [];

        foreach ($categoryBreakdown as $idx => $cat) {
            $sales = (float) $cat->total_sales;
            $catLabels[] = $cat->name;
            $catSeries[] = $sales;
            $pct = $totalCatSales > 0 ? round(($sales / $totalCatSales) * 100) : 0;
            $catList[] = [
                'name'  => $cat->name,
                'total' => $sales,
                'pct'   => $pct,
                'color' => $colors[$idx % count($colors)],
            ];
        }

        // If categories have 0 sales yet, provide product count per category as distribution
        if ($totalCatSales == 0) {
            $catProductCounts = DB::table('categories')
                ->leftJoin('products', 'categories.id', '=', 'products.category_id')
                ->select('categories.name', DB::raw('COUNT(products.id) as count'))
                ->groupBy('categories.id', 'categories.name')
                ->take(5)
                ->get();

            $totalProducts = $catProductCounts->sum('count');
            $catLabels = [];
            $catSeries = [];
            $catList = [];
            foreach ($catProductCounts as $idx => $cat) {
                $count = (int) $cat->count;
                $catLabels[] = $cat->name;
                $catSeries[] = $count;
                $pct = $totalProducts > 0 ? round(($count / $totalProducts) * 100) : 0;
                $catList[] = [
                    'name'  => $cat->name,
                    'total' => $count,
                    'pct'   => $pct,
                    'color' => $colors[$idx % count($colors)],
                ];
            }
        }

        // 4. Top Performing Vendors
        $topVendors = DB::table('vendors')
            ->leftJoin('order_items', 'vendors.id', '=', 'order_items.vendor_id')
            ->select(
                'vendors.id',
                'vendors.store_name as name',
                DB::raw('COALESCE(SUM(order_items.price * order_items.quantity), 0) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as orders_count')
            )
            ->groupBy('vendors.id', 'vendors.store_name')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        $maxVendorRev = $topVendors->max('total_revenue') ?: 1;
        foreach ($topVendors as $v) {
            $v->sales_percent = round(($v->total_revenue / $maxVendorRev) * 100);
        }

        // 5. Recent Activity from actual DB
        $recentOrders = Order::with('user')->latest()->take(3)->get();
        $recentVendors = Vendor::latest()->take(3)->get();

        $activities = [];
        foreach ($recentOrders as $ro) {
            $userName = $ro->user ? $ro->user->name : 'Customer';
            $activities[] = [
                'icon'  => 'ti-shopping-bag',
                'color' => 'success',
                'text'  => "Order #{$ro->id} placed by {$userName} (\${$ro->total})",
                'time'  => $ro->created_at->diffForHumans(),
            ];
        }
        foreach ($recentVendors as $rv) {
            $activities[] = [
                'icon'  => 'ti-building-store',
                'color' => 'primary',
                'text'  => "Vendor \"{$rv->store_name}\" ({$rv->approval_status})",
                'time'  => $rv->created_at->diffForHumans(),
            ];
        }

        return view('admin.reports.index', [
            'stats'         => $stats,
            'months'        => $months,
            'revenueData'   => $revenueData,
            'ordersData'    => $ordersData,
            'catLabels'     => $catLabels,
            'catSeries'     => $catSeries,
            'catList'       => $catList,
            'topVendors'    => $topVendors,
            'activities'    => $activities,
        ]);
    }
}
