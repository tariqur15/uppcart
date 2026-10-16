<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Storage;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $allBrands = Brand::withCount('products')->orderBy('name')->get();

        if ($search = $request->input('search')) {
            $matchingIds = $allBrands->filter(fn ($b) => str_contains(strtolower($b->name), strtolower($search)) ||
                str_contains(strtolower($b->slug), strtolower($search))
            )->pluck('id')->toArray();

            $allBrands = $allBrands->filter(function ($b) use ($matchingIds, $allBrands) {
                if (in_array($b->id, $matchingIds)) {
                    return true;
                }
                $visited = [];
                $parent = $allBrands->firstWhere('id', $b->parent_id);
                while ($parent && ! in_array($parent->id, $visited, true)) {
                    $visited[] = $parent->id;
                    if (in_array($parent->id, $matchingIds)) {
                        return true;
                    }
                    $parent = $allBrands->firstWhere('id', $parent->parent_id);
                }

                return false;
            });
        }

        $brands = collect($this->buildBrandTree($allBrands));

        return view('admin.brands.index', compact('brands'));
    }

    private function buildBrandTree($brands, $parentId = null, $depth = 0): array
    {
        if ($depth > 10) {
            return [];
        }

        $result = [];
        foreach ($brands as $brand) {
            if ($brand->parent_id === $parentId) {
                $brand->depth = $depth;
                $result[] = $brand;
                $result = array_merge($result, $this->buildBrandTree($brands, $brand->id, $depth + 1));
            }
        }

        return $result;
    }

    public function create(): View
    {
        $parentBrands = Brand::orderBy('name')->get();

        return view('admin.brands.create', compact('parentBrands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'parent_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('brands', 'public');
        }

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        $parentBrands = Brand::where('id', '!=', $brand->id)->orderBy('name')->get();

        return view('admin.brands.edit', compact('brand', 'parentBrands'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,'.$brand->id,
            'slug' => 'nullable|string|max:255|unique:brands,slug,'.$brand->id,
            'parent_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }

            $validated['image'] = $request->file('image')->store('brands', 'public');
        } elseif ($request->boolean('remove_image') && $brand->image) {
            Storage::disk('public')->delete($brand->image);
            $validated['image'] = null;
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'Cannot delete brand with existing products.');
        }

        if ($brand->image) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:brands,id',
        ]);

        $brands = Brand::withCount('products')->whereIn('id', $request->ids)->get();

        foreach ($brands as $brand) {
            if ($brand->products_count > 0) {
                return redirect()->route('admin.brands.index')
                    ->with('error', "Cannot delete \"{$brand->name}\" — it has {$brand->products_count} product(s).");
            }

            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
        }

        Brand::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', count($request->ids).' brand(s) deleted successfully.');
    }
}
