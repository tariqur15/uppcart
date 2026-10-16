<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $baseQuery = Order::whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);

        $totalRevenue = (clone $baseQuery)->whereIn('status', ['completed', 'shipped', 'delivered'])->sum('total_price');
        $totalOrders = (clone $baseQuery)->count();
        $completedOrders = (clone $baseQuery)->whereIn('status', ['completed', 'shipped', 'delivered'])->count();

        $totalItems = OrderItem::whereHas('order', function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        })->sum('quantity');

        $avgOrderValue = $completedOrders > 0 ? $totalRevenue / $completedOrders : 0;

        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $dailySales = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date, count(*) as orders, sum(total_price) as revenue')
            ->whereIn('status', ['completed', 'shipped', 'delivered'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = OrderItem::selectRaw('product_id, sum(quantity) as total_qty, sum(price * quantity) as total_revenue')
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                    ->whereIn('status', ['completed', 'shipped', 'delivered']);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(10)
            ->with('product')
            ->get();

        $thisPeriod = now()->setDateFrom(now()->startOfMonth())->format('F Y');

        return view('admin.sales-reports.index', compact(
            'from',
            'to',
            'totalRevenue',
            'totalOrders',
            'completedOrders',
            'totalItems',
            'avgOrderValue',
            'statusCounts',
            'dailySales',
            'topProducts',
            'thisPeriod',
        ));
    }

    public function export(Request $request): RedirectResponse
    {
        return redirect()->route('admin.sales-reports.index')->with('success', 'Export feature coming soon.');
    }
}
