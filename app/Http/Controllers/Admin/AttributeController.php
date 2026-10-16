<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttributeRequest;
use App\Http\Requests\Admin\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttributeController extends Controller
{
    public function index(): View
    {
        $attributes = Attribute::with('options')
            ->withCount('options')
            ->latest()
            ->paginate(15);

        return view('admin.attributes.index', compact('attributes'));
    }

    public function create(): View
    {
        return view('admin.attributes.create');
    }

    public function store(StoreAttributeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $attribute = Attribute::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'value_type' => $validated['value_type'],
        ]);

        if (! empty($validated['options'])) {
            $this->saveOptions($attribute, $validated['options'], $request);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute created successfully.');
    }

    public function edit(Attribute $attribute): View
    {
        $attribute->load('options');

        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute): RedirectResponse
    {
        $validated = $request->validated();

        $attribute->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'value_type' => $validated['value_type'],
        ]);

        if (! empty($request->input('options'))) {
            $this->saveOptions($attribute, $request->input('options'), $request);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute deleted successfully.');
    }

    protected function saveOptions(Attribute $attribute, array $options, Request $request): void
    {
        foreach ($options as $index => $option) {
            $imagePath = null;
            $imageKey = "options.{$index}.image";

            if ($request->hasFile($imageKey)) {
                $imagePath = $request->file($imageKey)->store('attribute-options', 'public');
            }

            $attribute->options()->create([
                'display_name' => $option['display_name'],
                'option_type' => $option['option_type'],
                'text_value' => $option['text_value'] ?? null,
                'color_value' => $option['color_value'] ?? null,
                'image_path' => $imagePath,
                'sort_order' => $index,
            ]);
        }
    }
}
