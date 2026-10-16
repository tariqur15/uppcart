<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Tag;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Storage;

class CollectionController extends Controller
{
    public function __construct(
        protected CollectionService $collectionService
    ) {}

    public function index(): View
    {
        $collections = Collection::withCount('products')
            ->latest()
            ->paginate(15);

        return view('admin.collections.index', compact('collections'));
    }

    public function create(): View
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $service = CollectionService::class;

        return view('admin.collections.create', compact('brands', 'categories', 'tags', 'service'));
    }

    public function store(StoreCollectionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('collections', 'public');
        }

        $collection = Collection::create($validated);

        if ($collection->collection_type === 'manual' && $request->filled('products')) {
            $products = collect($request->input('products'))
                ->map(fn ($id, $idx) => ['product_id' => $id, 'sort_order' => $idx]);

            $collection->collectionProducts()->createMany($products->toArray());
        }

        if ($collection->collection_type === 'automatic' && $request->filled('conditions')) {
            $conditions = collect($request->input('conditions'))
                ->map(fn ($cond) => [
                    'condition_field' => $cond['field'],
                    'condition_operator' => $cond['operator'],
                    'condition_value' => $cond['value'],
                ]);

            $collection->conditions()->createMany($conditions->toArray());
        }

        return redirect()->route('admin.collections.index')
            ->with('success', 'Collection created successfully.');
    }

    public function edit(Collection $collection): View
    {
        $collection->load(['conditions', 'collectionProducts.product']);
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $service = CollectionService::class;

        return view('admin.collections.edit', compact('collection', 'brands', 'categories', 'tags', 'service'));
    }

    public function update(UpdateCollectionRequest $request, Collection $collection): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            if ($collection->image) {
                Storage::disk('public')->delete($collection->image);
            }

            $validated['image'] = $request->file('image')->store('collections', 'public');
        } elseif ($request->boolean('remove_image') && $collection->image) {
            Storage::disk('public')->delete($collection->image);
            $validated['image'] = null;
        }

        if ($collection->collection_type === 'automatic' && $collection->match_type !== $validated['match_type']) {
            $validated['match_type'] = $validated['match_type'];
        }

        $collection->update($validated);

        // Sync manual products
        if ($collection->collection_type === 'manual') {
            $collection->collectionProducts()->delete();

            if ($request->filled('products')) {
                $products = collect($request->input('products'))
                    ->map(fn ($id, $idx) => ['product_id' => $id, 'sort_order' => $idx]);

                $collection->collectionProducts()->createMany($products->toArray());
            }
        }

        // Sync conditions
        if ($collection->collection_type === 'automatic') {
            $collection->conditions()->delete();

            if ($request->filled('conditions')) {
                $conditions = collect($request->input('conditions'))
                    ->map(fn ($cond) => [
                        'condition_field' => $cond['field'],
                        'condition_operator' => $cond['operator'],
                        'condition_value' => $cond['value'],
                    ]);

                $collection->conditions()->createMany($conditions->toArray());
            }
        }

        return redirect()->route('admin.collections.index')
            ->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        if ($collection->image) {
            Storage::disk('public')->delete($collection->image);
        }

        $collection->delete();

        return redirect()->route('admin.collections.index')
            ->with('success', 'Collection deleted successfully.');
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $page = $request->input('page', 1);
        $perPage = 10;

        $products = Product::with(['category', 'brand'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$query}%"));
                });
            })
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'products' => $products->items(),
            'has_more' => $products->hasMorePages(),
            'next_page' => $products->currentPage() + 1,
            'total' => $products->total(),
        ]);
    }

    public function preview(Request $request, CollectionService $collectionService): JsonResponse
    {
        $conditions = $request->input('conditions', []);
        $matchType = $request->input('match_type', 'all');

        $products = $collectionService->previewAutomaticQuery($conditions, $matchType);

        $items = $products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'image' => $p->image ? Storage::url($p->image) : null,
            'brand' => $p->brand?->name,
            'price' => $p->sale_price ?? $p->price,
            'formatted_price' => '$'.number_format($p->sale_price ?? $p->price, 2),
            'status' => $p->status,
        ]);

        return response()->json([
            'products' => $items,
            'total' => $products->total(),
            'count' => $products->count(),
        ]);
    }
}
