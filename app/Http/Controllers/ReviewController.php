<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|max:5000',
            'title' => 'nullable|string|max:255',
        ]);

        $review = Review::create([
            'product_id' => $validated['product_id'],
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'body' => $validated['body'],
            'title' => $validated['title'] ?? null,
            'is_approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'review' => $review->load('user:id,name'),
        ]);
    }
}
