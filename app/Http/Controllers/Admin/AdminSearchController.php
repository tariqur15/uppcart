<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (! $query) {
            return response()->json([
                'products' => [],
                'categories' => [],
                'orders' => [],
            ]);
        }

        $products = Product::where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->take(5)
            ->get(['id', 'name', 'sku', 'price', 'sale_price', 'image'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'price' => $p->sale_price ?? $p->price,
                'image' => $p->image ? asset('storage/'.$p->image) : null,
                'url' => route('admin.products.edit', $p),
            ]);

        $categories = Category::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('slug', 'like', "%{$query}%");
            })
            ->take(5)
            ->get(['id', 'name', 'slug'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'url' => route('admin.categories.edit', $c),
            ]);

        $orders = Order::where(function ($q) use ($query) {
            $q->where('id', 'like', "%{$query}%")
                ->orWhere('status', 'like', "%{$query}%")
                ->orWhere('payment_status', 'like', "%{$query}%");
        })
            ->take(5)
            ->get(['id', 'order_number', 'user_id', 'total_price', 'status', 'payment_status'])
            ->load('user')
            ->map(fn ($o) => [
                'id' => $o->id,
                'order_number' => '#'.$o->order_number,
                'customer' => $o->user?->name ?? 'Guest',
                'status' => $o->status,
                'total' => $o->total_price,
                'url' => route('admin.orders.show', $o),
            ]);

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'orders' => $orders,
        ]);
    }
}
