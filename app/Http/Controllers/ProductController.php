<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductView;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $products = Product::where('status', 'active')
            ->with(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->when(request('category'), function ($query) {
                $query->whereHas('categories', function ($q) {
                    $q->where('slug', request('category'));
                });
            })
            ->when(request('brand'), function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('brand', function ($b) {
                        $b->where('slug', request('brand'));
                    })->orWhereHas('brands', function ($b) {
                        $b->where('slug', request('brand'));
                    });
                });
            })
            ->when(request('tag'), function ($query) {
                $query->whereHas('tags', function ($q) {
                    $q->where('slug', request('tag'));
                });
            })
            ->when(request('search'), function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.request('search').'%')
                        ->orWhere('description', 'like', '%'.request('search').'%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_if($product->status !== 'active', 404);

        $product->load([
            'category',
            'brand',
            'brands',
            'tags',
            'reviews' => fn ($q) => $q->where('is_approved', true)->latest(),
            'reviews.user',
            'attributeOptions.attribute',
            'tierPrices' => fn ($q) => $q->where('status', true)->orderBy('minimum_quantity'),
        ]);

        $this->trackProductView($product);

        $relatedProducts = Product::where('status', 'active')
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->whereHas('categories', fn ($c) => $c->whereIn('categories.id', $product->categories->pluck('id')))
                    ->orWhereHas('brands', fn ($b) => $b->whereIn('brands.id', $product->brands->pluck('id')))
                    ->orWhereHas('tags', fn ($t) => $t->whereIn('tags.id', $product->tags->pluck('id')));
            })
            ->with(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function searchAjax()
    {
        $query = request('q');

        if (! $query || strlen($query) < 2) {
            return response()->json(['products' => []]);
        }

        $products = Product::where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%');
            })
            ->with('category')
            ->take(6)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'image' => $product->image ? asset('storage/'.$product->image) : null,
                'url' => route('products.show', $product),
                'category' => $product->category->name,
            ]);

        return response()->json(['products' => $products]);
    }

    private function trackProductView(Product $product): void
    {
        $request = request();
        $userAgent = $request->userAgent() ?? '';
        $parsed = ProductView::parseUserAgent($userAgent);
        $referrer = $request->headers->get('referer');

        ProductView::create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'session_id' => $request->session()->getId(),
            'browser' => $parsed['browser'],
            'os' => $parsed['os'],
            'device_type' => $parsed['device_type'],
            'country' => null,
            'city' => null,
            'traffic_source' => ProductView::parseTrafficSource($referrer),
            'referrer_url' => $referrer,
            'landing_url' => $request->url(),
        ]);
    }
}
