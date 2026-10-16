<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductTierPrice;
use App\Models\ProductView;
use App\Models\Review;
use App\Models\Tag;
use App\Services\SkuGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 20);
        $perPage = min(max($perPage, 10), 100);

        $query = Product::with(['category', 'brand', 'collections', 'tags']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $stockStatus = $request->input('stock_status');

        if ($stockStatus === 'in_stock') {
            $query->where(function ($q) {
                $q->where('stock', '>', 0)->orWhere('type', 'digital');
            });
        } elseif ($stockStatus === 'out_of_stock') {
            $query->where('stock', '=', 0)->where('type', '!=', 'digital');
        }

        if ($stockFrom = $request->input('stock_from')) {
            $query->where('stock', '>=', (int) $stockFrom);
        }

        if ($stockTo = $request->input('stock_to')) {
            $query->where('stock', '<=', (int) $stockTo);
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        if (! in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $sortable = [
            'stock' => 'stock',
            'price' => DB::raw('COALESCE(sale_price, price)'),
            'created_at' => 'created_at',
        ];

        if ($sort === 'orders') {
            $query->withCount(['orderItems as total_orders_count'])
                ->orderBy('total_orders_count', $direction);
        } elseif ($sort === 'views') {
            $sevenDaysAgoSub = now()->subDays(7);
            $query->withCount(['productViews as views_7d_count' => function ($q) use ($sevenDaysAgoSub) {
                $q->where('created_at', '>=', $sevenDaysAgoSub);
            }])
                ->orderBy('views_7d_count', $direction);
        } elseif (isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->latest();
        }

        $products = $query->paginate($perPage)->withQueryString();

        $productIds = $products->pluck('id');

        $salesData = OrderItem::whereIn('product_id', $productIds)
            ->select('product_id', DB::raw('COUNT(DISTINCT order_id) as total_orders'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $ratingData = Review::whereIn('product_id', $productIds)->where('is_approved', true)
            ->select('product_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as review_count'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $sevenDaysAgo = now()->subDays(7);
        $viewData = ProductView::whereIn('product_id', $productIds)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->select('product_id', DB::raw('COUNT(*) as views_7d'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $stats = [
            'orders' => $salesData,
            'ratings' => $ratingData,
            'views' => $viewData,
        ];

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'perPage', 'stats', 'sort', 'direction'));
    }

    public function searchAjax(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (! $query || strlen($query) < 2) {
            return response()->json(['products' => []]);
        }

        $products = Product::with('category')
            ->where(function (Builder $q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('slug', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->take(8)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'stock' => $product->stock,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'status' => $product->status,
                'edit_url' => route('admin.products.edit', $product),
            ]);

        return response()->json(['products' => $products]);
    }

    public function create(): View
    {
        $categories = $this->hierarchicalCategories();
        $brands = $this->hierarchicalBrands();
        $collections = Collection::orderBy('name')->get();
        $tags = Tag::withCount('products')->orderBy('name')->get();
        $frequentlyUsedTags = $tags->sortByDesc('products_count')->take(10)->values();
        $attributes = Attribute::with('options')->get();

        return view('admin.products.create', compact('categories', 'brands', 'collections', 'tags', 'attributes', 'frequentlyUsedTags'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = ! empty($validated['seo_slug'])
            ? $validated['seo_slug']
            : Str::slug($validated['name']);

        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Product::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug.'-'.$counter;
            $counter++;
        }

        if (empty($validated['sku'] ?? null)) {
            $validated['sku'] = SkuGenerator::generate($validated['name']);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $newUploads = [];

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $newUploads[] = $file->store('products/gallery', 'public');
            }
        }

        $libraryPaths = $request->filled('gallery_library_paths')
            ? json_decode($request->input('gallery_library_paths'), true) ?? []
            : [];

        if ($request->filled('gallery_order')) {
            $order = explode(',', $request->gallery_order);
            $ordered = [];
            $newIdx = 0;
            $libIdx = 0;

            foreach ($order as $id) {
                if (str_starts_with($id, 'n:')) {
                    if (isset($newUploads[$newIdx])) {
                        $ordered[] = $newUploads[$newIdx];
                        $newIdx++;
                    }
                } elseif (str_starts_with($id, 'lib:')) {
                    if (isset($libraryPaths[$libIdx])) {
                        $ordered[] = $libraryPaths[$libIdx];
                        $libIdx++;
                    }
                }
            }

            $validated['gallery_images'] = $ordered;
        } else {
            $gallery = $newUploads;

            if (! empty($libraryPaths)) {
                $gallery = array_merge($gallery, $libraryPaths);
            }

            $validated['gallery_images'] = $gallery;
        }

        if ($request->filled('featured_library_path')) {
            $validated['image'] = $request->input('featured_library_path');
        }

        if (isset($validated['variant_options']) && is_string($validated['variant_options'])) {
            $validated['variant_options'] = json_decode($validated['variant_options'], true);
        }

        if (isset($validated['variant_data']) && is_string($validated['variant_data'])) {
            $validated['variant_data'] = json_decode($validated['variant_data'], true);
        }

        if (isset($validated['digital_files']) && is_string($validated['digital_files'])) {
            $validated['digital_files'] = json_decode($validated['digital_files'], true);
        }

        if (isset($validated['custom_fields']) && is_string($validated['custom_fields'])) {
            $validated['custom_fields'] = json_decode($validated['custom_fields'], true);
        }

        $validated['featured'] = $request->boolean('featured');
        $validated['custom_fields_enabled'] = $request->boolean('custom_fields_enabled');
        $validated['show_stock'] = $request->boolean('show_stock');

        if (empty($validated['initial_stock'] ?? null)) {
            $validated['initial_stock'] = $validated['stock'];
        }

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $collectionIds = $validated['collections'] ?? [];
        unset($validated['collections']);

        $categoryIds = $validated['categories'] ?? [];
        unset($validated['categories']);

        if (empty($categoryIds)) {
            $categoryIds = [$validated['category_id'] ?? null];
        }

        $brandIds = $validated['brands'] ?? [];
        unset($validated['brands']);

        if (empty($brandIds)) {
            $brandIds = isset($validated['brand_id']) ? [$validated['brand_id']] : [];
        }

        $attributeOptions = $validated['attribute_options'] ?? [];
        unset($validated['attribute_options']);

        $tierPrices = $validated['tier_prices'] ?? [];
        unset($validated['tier_prices']);

        $validated['category_id'] = $categoryIds[0] ?? $validated['category_id'];
        $validated['brand_id'] = $brandIds[0] ?? null;

        $product = Product::create($validated);

        if (! empty($tags)) {
            $product->tags()->sync($tags);
        }

        if (! empty($collectionIds)) {
            $product->collections()->sync($collectionIds);
        }

        if (! empty($categoryIds)) {
            $product->categories()->sync($categoryIds);
        }

        if (! empty($brandIds)) {
            $product->brands()->sync($brandIds);
        }

        if (! empty($attributeOptions)) {
            $product->attributeOptions()->sync($attributeOptions);
        }

        $this->syncTierPrices($product, $tierPrices);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $categories = $this->hierarchicalCategories();
        $brands = $this->hierarchicalBrands();
        $collections = Collection::orderBy('name')->get();
        $selectedCollectionIds = old('collections', $product->collections->pluck('id')->toArray());
        $selectedCategoryIds = old('categories', $product->categories->pluck('id')->toArray());
        $selectedBrandIds = old('brands', $product->brands->pluck('id')->toArray());
        $tags = Tag::withCount('products')->orderBy('name')->get();
        $frequentlyUsedTags = $tags->sortByDesc('products_count')->take(10)->values();
        $attributes = Attribute::with('options')->get();
        $selectedAttributeOptions = $product->attributeOptions->pluck('id')->toArray();

        $productOrderItems = OrderItem::where('product_id', $product->id)
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('orders.id as order_id, orders.status as order_status, order_items.quantity as item_quantity')
            ->get();

        $activeStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed'];

        $totalItems = $productOrderItems;
        $cancelledItems = $productOrderItems->where('order_status', 'cancelled');
        $returnedItems = $productOrderItems->where('order_status', 'returned');

        $timeline = [
            'total_orders' => $totalItems->unique('order_id')->count(),
            'total_quantity' => $totalItems->sum('item_quantity'),
            'cancelled_orders' => $cancelledItems->unique('order_id')->count(),
            'cancelled_quantity' => $cancelledItems->sum('item_quantity'),
            'returned_orders' => $returnedItems->unique('order_id')->count(),
            'returned_quantity' => $returnedItems->sum('item_quantity'),
            'initial_stock' => $product->initial_stock,
            'current_stock' => $product->stock,
        ];

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'collections', 'selectedCollectionIds', 'selectedCategoryIds', 'selectedBrandIds', 'tags', 'attributes', 'selectedAttributeOptions', 'frequentlyUsedTags', 'timeline'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = ! empty($validated['seo_slug'])
            ? $validated['seo_slug']
            : Str::slug($validated['name']);

        if ($product->slug !== $validated['slug']) {
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Product::where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
                $validated['slug'] = $originalSlug.'-'.$counter;
                $counter++;
            }
        }

        if (empty($validated['sku'] ?? null)) {
            $validated['sku'] = SkuGenerator::generate($validated['name']);
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('remove_featured') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }

        $existingGallery = $product->gallery_images ?? [];

        if ($request->has('remove_gallery')) {
            foreach ($request->remove_gallery as $path) {
                Storage::disk('public')->delete($path);
            }

            $existingGallery = array_diff($existingGallery, $request->remove_gallery);
        }

        $newUploads = [];

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $newUploads[] = $file->store('products/gallery', 'public');
            }
        }

        $libraryPaths = $request->filled('gallery_library_paths')
            ? json_decode($request->input('gallery_library_paths'), true) ?? []
            : [];

        if ($request->filled('gallery_order')) {
            $order = explode(',', $request->gallery_order);
            $ordered = [];
            $newIdx = 0;
            $libIdx = 0;

            foreach ($order as $id) {
                if (str_starts_with($id, 'e:')) {
                    $idx = (int) substr($id, 2);

                    if (isset($existingGallery[$idx])) {
                        $ordered[] = $existingGallery[$idx];
                    }
                } elseif (str_starts_with($id, 'n:')) {
                    if (isset($newUploads[$newIdx])) {
                        $ordered[] = $newUploads[$newIdx];
                        $newIdx++;
                    }
                } elseif (str_starts_with($id, 'lib:')) {
                    if (isset($libraryPaths[$libIdx])) {
                        $ordered[] = $libraryPaths[$libIdx];
                        $libIdx++;
                    }
                }
            }

            $existingGallery = $ordered;
        } else {
            if (! empty($newUploads)) {
                $existingGallery = array_merge($existingGallery, $newUploads);
            }

            if (! empty($libraryPaths)) {
                $existingGallery = array_merge($existingGallery, $libraryPaths);
            }
        }

        $validated['gallery_images'] = $existingGallery;

        if ($request->filled('featured_library_path')) {
            $validated['image'] = $request->input('featured_library_path');
        }

        if (isset($validated['variant_options']) && is_string($validated['variant_options'])) {
            $validated['variant_options'] = json_decode($validated['variant_options'], true);
        }

        if (isset($validated['variant_data']) && is_string($validated['variant_data'])) {
            $validated['variant_data'] = json_decode($validated['variant_data'], true);
        }

        if (isset($validated['digital_files']) && is_string($validated['digital_files'])) {
            $validated['digital_files'] = json_decode($validated['digital_files'], true);
        }

        if (isset($validated['custom_fields']) && is_string($validated['custom_fields'])) {
            $validated['custom_fields'] = json_decode($validated['custom_fields'], true);
        }

        $validated['featured'] = $request->boolean('featured');
        $validated['custom_fields_enabled'] = $request->boolean('custom_fields_enabled');
        $validated['show_stock'] = $request->boolean('show_stock');

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $collectionIds = $validated['collections'] ?? [];
        unset($validated['collections']);

        $categoryIds = $validated['categories'] ?? [];
        unset($validated['categories']);

        if (empty($categoryIds)) {
            $categoryIds = [$validated['category_id'] ?? null];
        }

        $brandIds = $validated['brands'] ?? [];
        $hasBrandField = array_key_exists('brands', $validated) || array_key_exists('brand_id', $validated);
        unset($validated['brands']);

        if (empty($brandIds)) {
            $brandIds = isset($validated['brand_id']) ? [$validated['brand_id']] : [];
        }

        $attributeOptions = $validated['attribute_options'] ?? [];
        unset($validated['attribute_options']);

        $tierPrices = $validated['tier_prices'] ?? [];
        unset($validated['tier_prices']);

        $validated['category_id'] = $categoryIds[0] ?? $validated['category_id'];
        $validated['brand_id'] = $brandIds[0] ?? null;

        $product->update($validated);

        $product->tags()->sync($tags);

        $product->collections()->sync($collectionIds);

        $product->categories()->sync($categoryIds);

        if ($hasBrandField) {
            $product->brands()->sync($brandIds);
        }

        $product->attributeOptions()->sync($attributeOptions);

        $this->syncTierPrices($product, $tierPrices);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function slugCheck(Request $request): JsonResponse
    {
        $slug = $request->input('slug');
        $productId = $request->input('product_id');

        $query = Product::where('slug', $slug);

        if ($productId) {
            $query->where('id', '!=', $productId);
        }

        return response()->json([
            'available' => ! $query->exists(),
        ]);
    }

    public function uploadCustomFieldFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $path = $request->file('file')->store('products/custom-fields', 'public');

        return response()->json([
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function uploadDigitalFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:zip,pdf,mp4,rar,7z,doc,docx|max:51200',
        ]);

        $path = $request->file('file')->store('products/digital', 'public');

        return response()->json([
            'path' => $path,
            'name' => $request->file('file')->getClientOriginalName(),
        ]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        if ($product->gallery_images) {
            foreach ($product->gallery_images as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'status' => $product->status === 'active' ? 'draft' : 'active',
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product status updated.');
    }

    public function toggleFeatured(Product $product): JsonResponse
    {
        $product->update([
            'featured' => ! $product->featured,
        ]);

        return response()->json([
            'featured' => $product->featured,
        ]);
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        $products = Product::whereIn('id', $request->ids)->get();

        foreach ($products as $product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            if ($product->gallery_images) {
                foreach ($product->gallery_images as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        Product::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.products.index')
            ->with('success', count($request->ids).' product(s) deleted successfully.');
    }

    public function bulkToggleStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'status' => 'required|in:active,draft',
        ]);

        Product::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->route('admin.products.index')
            ->with('success', count($request->ids).' product(s) '.$request->status.'d successfully.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'nullable|in:active,draft',
            'stock' => 'nullable|integer|min:0',
            'sale_price' => 'nullable|numeric|min:0',
        ]);

        $data = array_filter($request->only(['category_id', 'status', 'stock', 'sale_price']), fn ($value) => $value !== null);

        if (empty($data)) {
            return redirect()->route('admin.products.index')
                ->with('error', 'No fields selected to update.');
        }

        Product::whereIn('id', $request->ids)->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', count($request->ids).' product(s) updated successfully.');
    }

    public function recentMedia(): JsonResponse
    {
        $paths = collect(Storage::disk('public')->files('products'))
            ->merge(Storage::disk('public')->files('products/gallery'))
            ->filter(fn ($path) => in_array(
                strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']
            ))
            ->values();

        $media = $paths->map(fn ($path) => [
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'name' => basename($path),
            'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
        ]);

        return response()->json(['images' => $media]);
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('file')->store('products/gallery', 'public');

        return response()->json([
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'name' => basename($path),
            'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
        ]);
    }

    public function deleteMedia(Request $request): JsonResponse
    {
        $path = $request->input('path');

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        if (str_contains($path, '..')) {
            return response()->json(['error' => 'Invalid path.'], 400);
        }

        Storage::disk('public')->delete($path);

        return response()->json(['success' => true]);
    }

    private function hierarchicalBrands(): EloquentCollection
    {
        $all = Brand::orderBy('name')->get();
        $result = new EloquentCollection;

        $this->buildBrandHierarchy($all, null, '', $result);

        return $result;
    }

    private function buildBrandHierarchy(EloquentCollection $all, $parentId, string $prefix, EloquentCollection $result, int $depth = 0): void
    {
        if ($depth > 10) {
            return;
        }

        foreach ($all as $brand) {
            if ($brand->parent_id === $parentId) {
                $brand->hierarchy_prefix = $prefix;
                $result->push($brand);
                $this->buildBrandHierarchy($all, $brand->id, $prefix.'— ', $result, $depth + 1);
            }
        }
    }

    private function hierarchicalCategories(): EloquentCollection
    {
        $all = Category::orderBy('name')->get();
        $result = new EloquentCollection;

        $this->buildHierarchy($all, null, '', $result);

        return $result;
    }

    private function buildHierarchy(EloquentCollection $all, $parentId, string $prefix, EloquentCollection $result, int $depth = 0): void
    {
        if ($depth > 10) {
            return;
        }

        foreach ($all as $category) {
            if ($category->parent_id === $parentId) {
                $category->hierarchy_prefix = $prefix;
                $result->push($category);
                $this->buildHierarchy($all, $category->id, $prefix.'— ', $result, $depth + 1);
            }
        }
    }

    public function analytics(Product $product, Request $request): JsonResponse
    {
        $period = $request->input('period', '7d');
        $since = match ($period) {
            '24h' => now()->subHours(24),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => now()->subDays(7),
        };

        $views = ProductView::where('product_id', $product->id)
            ->where('created_at', '>=', $since);

        $totalViews = (clone $views)->count();
        $uniqueVisitors = (clone $views)->distinct('ip_address')->count('ip_address');

        $viewsByDay = ProductView::where('product_id', $product->id)
            ->where('created_at', '>=', $since)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT ip_address) as unique_visitors'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $byBrowser = ProductView::where('product_id', $product->id)
            ->where('created_at', '>=', $since)
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->get();

        $byDevice = ProductView::where('product_id', $product->id)
            ->where('created_at', '>=', $since)
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();

        $byOs = ProductView::where('product_id', $product->id)
            ->where('created_at', '>=', $since)
            ->select('os', DB::raw('COUNT(*) as count'))
            ->groupBy('os')
            ->orderByDesc('count')
            ->get();

        $byTrafficSource = ProductView::where('product_id', $product->id)
            ->where('created_at', '>=', $since)
            ->select('traffic_source', DB::raw('COUNT(*) as count'))
            ->groupBy('traffic_source')
            ->orderByDesc('count')
            ->get();

        $byCountry = ProductView::where('product_id', $product->id)
            ->where('created_at', '>=', $since)
            ->whereNotNull('country')
            ->select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        $byCity = ProductView::where('product_id', $product->id)
            ->where('created_at', '>=', $since)
            ->whereNotNull('city')
            ->select('city', DB::raw('COUNT(*) as count'))
            ->groupBy('city')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
            ],
            'period' => $period,
            'total_views' => $totalViews,
            'unique_visitors' => $uniqueVisitors,
            'views_by_day' => $viewsByDay,
            'by_browser' => $byBrowser,
            'by_device' => $byDevice,
            'by_os' => $byOs,
            'by_traffic_source' => $byTrafficSource,
            'by_country' => $byCountry,
            'by_city' => $byCity,
        ]);
    }

    public function tierPricing(Product $product): JsonResponse
    {
        $tiers = $product->tierPrices()->orderBy('minimum_quantity')->get();

        return response()->json([
            'tier_pricing' => $tiers->map(fn (ProductTierPrice $tier) => [
                'id' => $tier->id,
                'min' => $tier->minimum_quantity,
                'max' => $tier->maximum_quantity,
                'type' => $tier->discount_type,
                'value' => (float) $tier->discount_value,
                'price' => (float) $tier->fixed_price,
                'status' => $tier->status,
            ]),
        ]);
    }

    private function syncTierPrices(Product $product, array $tierPrices): void
    {
        $existingIds = $product->tierPrices()->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($tierPrices as $index => $tier) {
            $basePrice = (float) ($product->sale_price ?? $product->price);

            $finalPrice = match ($tier['discount_type']) {
                'percentage' => $basePrice * (1 - $tier['discount_value'] / 100),
                'fixed_amount' => $basePrice - $tier['discount_value'],
                'fixed_price' => $tier['discount_value'],
                default => $basePrice,
            };

            $data = [
                'minimum_quantity' => $tier['minimum_quantity'],
                'maximum_quantity' => $tier['maximum_quantity'] ?? null,
                'discount_type' => $tier['discount_type'],
                'discount_value' => $tier['discount_value'],
                'fixed_price' => max(0, $finalPrice),
                'priority' => $index,
                'status' => $tier['status'] ?? true,
            ];

            if (! empty($tier['id'])) {
                $product->tierPrices()->where('id', $tier['id'])->update($data);
                $submittedIds[] = $tier['id'];
            } else {
                $created = $product->tierPrices()->create($data);
                $submittedIds[] = $created->id;
            }
        }

        $toDelete = array_diff($existingIds, $submittedIds);

        if (! empty($toDelete)) {
            ProductTierPrice::whereIn('id', $toDelete)->delete();
        }
    }
}
