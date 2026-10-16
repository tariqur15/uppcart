<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $orders = $user->orders()->latest()->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_spent' => $orders->sum('total_price'),
            'pending' => $orders->where('status', 'pending')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'shipped' => $orders->where('status', 'shipped')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
        ];

        $recentOrders = $orders->take(5);

        $products = Product::where('status', 'active')
            ->orderByRaw('RAND()')
            ->limit(4)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'price' => $p->sale_price > 0 ? (float) $p->sale_price : (float) $p->price,
                'original_price' => $p->sale_price > 0 ? (float) $p->price : null,
                'image' => $p->image ? asset('storage/'.$p->image) : null,
            ]);

        $currencySymbol = ViewFacade::shared('currencySymbol', '$');

        return view('dashboard', compact('user', 'stats', 'recentOrders', 'products', 'currencySymbol'));
    }
}
