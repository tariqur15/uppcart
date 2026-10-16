<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['product:id,name', 'user:id,name']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        if ($request->has('approved')) {
            $query->where('is_approved', filter_var($request->input('approved'), FILTER_VALIDATE_BOOLEAN));
        }

        $reviews = $query->latest()->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'price', 'image']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.create', compact('products', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'body' => 'required|string',
            'is_approved' => 'boolean',
        ]);

        $validated['is_approved'] = $request->has('is_approved');

        if ($request->filled('customer_name') && ! $request->filled('user_id')) {
            $user = User::create([
                'name' => $request->customer_name,
                'email' => $request->customer_email ?? $request->customer_name.'@guest',
                'password' => bcrypt(\Str::random(16)),
            ]);
            $validated['user_id'] = $user->id;
        }

        unset($validated['customer_name'], $validated['customer_email']);

        Review::create($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review created successfully.');
    }

    public function show(Review $review): View
    {
        $review->load(['product:id,name,image', 'user:id,name,email']);

        return view('admin.reviews.show', compact('review'));
    }

    public function edit(Review $review): View
    {
        $review->load(['product:id,name', 'user:id,name,email']);

        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'body' => 'required|string',
            'is_approved' => 'boolean',
        ]);

        $validated['is_approved'] = $request->has('is_approved');

        $review->update($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reviews,id',
        ]);

        Review::whereIn('id', $request->ids)->update(['is_approved' => true]);

        return redirect()->route('admin.reviews.index')
            ->with('success', count($request->ids).' review(s) approved successfully.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reviews,id',
        ]);

        Review::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', count($request->ids).' review(s) deleted successfully.');
    }
}
