<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $allCategories = Category::withCount('products')->orderBy('sort_order')->orderBy('name')->get();

        if ($search = $request->input('search')) {
            $matchingIds = $allCategories->filter(fn ($c) => str_contains(strtolower($c->name), strtolower($search)) ||
                str_contains(strtolower($c->slug), strtolower($search))
            )->pluck('id')->toArray();

            $allCategories = $allCategories->filter(function ($c) use ($matchingIds, $allCategories) {
                if (in_array($c->id, $matchingIds)) {
                    return true;
                }
                $visited = [];
                $parent = $allCategories->firstWhere('id', $c->parent_id);
                while ($parent && ! in_array($parent->id, $visited, true)) {
                    $visited[] = $parent->id;
                    if (in_array($parent->id, $matchingIds)) {
                        return true;
                    }
                    $parent = $allCategories->firstWhere('id', $parent->parent_id);
                }

                return false;
            });
        }

        $categories = collect($this->buildCategoryList($allCategories));

        return view('admin.categories.index', compact('categories'));
    }

    private function buildCategoryList($categories, $parentId = null, $depth = 0): array
    {
        if ($depth > 10) {
            return [];
        }

        $result = [];
        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $category->depth = $depth;
                $result[] = $category;
                $result = array_merge($result, $this->buildCategoryList($categories, $category->id, $depth + 1));
            }
        }

        return $result;
    }

    public function create(): View
    {
        $allCategories = Category::orderBy('name')->get();
        $parentCategories = $this->buildCategoryTree($allCategories);

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $allCategories = Category::where('id', '!=', $category->id)->orderBy('name')->get();
        $parentCategories = $this->buildCategoryTree($allCategories);

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    private function buildCategoryTree($categories, $parentId = null, $prefix = '', int $depth = 0): array
    {
        if ($depth > 10) {
            return [];
        }

        $result = [];
        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $category->hierarchy_prefix = $prefix;
                $result[] = $category;
                $result = array_merge($result, $this->buildCategoryTree($categories, $category->id, $prefix.'— ', $depth + 1));
            }
        }

        return $result;
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,'.$category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        if ($request->boolean('remove_image') && $category->image) {
            Storage::disk('public')->delete($category->image);
            $data['image'] = null;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with existing products.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleFeatured(Category $category): JsonResponse
    {
        $category->update(['is_featured' => ! $category->is_featured]);

        return response()->json([
            'is_featured' => $category->fresh()->is_featured,
        ]);
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        $categories = Category::withCount('products')->whereIn('id', $request->ids)->get();

        foreach ($categories as $category) {
            if ($category->products_count > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', "Cannot delete \"{$category->name}\" — it has {$category->products_count} product(s).");
            }

            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
        }

        Category::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', count($request->ids).' category(ies) deleted successfully.');
    }
}
