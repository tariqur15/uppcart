<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::whereIn('status', ['completed', 'shipped'])
                ->sum('total_price'),
            'totalUsers' => User::count(),
            'totalCategories' => Category::count(),
            'recentOrders' => Order::with('user')->latest()->take(5)->get(),
            'lowStockProducts' => Product::where('stock', '<', 10)->where('status', 'active')->take(5)->get(),
        ]);
    }
}
