<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();
        $categories = Category::withCount('products')->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->take(8)->get();
        $latestProducts = Product::where('status', 'active')
            ->with(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->latest()
            ->take(8)
            ->get();
        $featuredProducts = Product::where('status', 'active')
            ->where('featured', 1)
            ->with('brand')
            ->with(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->latest()
            ->get();
        $totalProducts = Product::where('status', 'active')->count();
        $avgRating = Review::where('is_approved', true)->avg('rating');
        $totalReviews = Review::where('is_approved', true)->count();
        $testimonials = Review::where('is_approved', true)
            ->with('product', 'user')
            ->latest()
            ->take(4)
            ->get();
        $collections = Collection::active()
            ->with(['products' => function ($query) {
                $query->where('status', 'active')
                    ->with(['reviews' => fn ($q) => $q->where('is_approved', true)])
                    ->take(8);
            }])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($c) => $c->products->isNotEmpty());

        $bannersJson = $banners->map(fn ($b) => [
            'title' => $b->title,
            'subtitle' => $b->subtitle,
            'link' => $b->link,
            'image' => $b->image,
            'bg_color' => $b->bg_color,
        ]);

        return view('home', compact(
            'banners',
            'bannersJson',
            'categories',
            'latestProducts',
            'featuredProducts',
            'totalProducts',
            'avgRating',
            'totalReviews',
            'testimonials',
            'collections'
        ));
    }
}
