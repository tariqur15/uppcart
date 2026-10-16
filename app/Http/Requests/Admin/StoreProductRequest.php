<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        if (is_string($this->digital_files) && $this->digital_files !== '') {
            $decoded = json_decode($this->digital_files, true);
            $this->merge(['digital_files' => $decoded]);
        }

        if (is_string($this->custom_fields) && $this->custom_fields !== '') {
            $decoded = json_decode($this->custom_fields, true);
            $this->merge(['custom_fields' => $decoded]);
        }

        if (is_string($this->tier_prices) && $this->tier_prices !== '') {
            $decoded = json_decode($this->tier_prices, true);
            $this->merge(['tier_prices' => $decoded]);
        }

        if ($this->type === 'digital') {
            $this->merge(['stock' => 0]);
        }
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'brands' => 'nullable|array',
            'brands.*' => 'exists:brands,id',
            'collection_id' => 'nullable|exists:collections,id',
            'collections' => 'nullable|array',
            'collections.*' => 'exists:collections,id',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'type' => 'required|in:physical,digital,variable',
            'description' => 'nullable|string',
            'short_description' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (strlen(strip_tags($value)) > 500) {
                    $fail('The '.$attribute.' must not exceed 500 characters.');
                }
            }],
            'long_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'show_stock' => 'nullable|boolean',
            'initial_stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'digital_files' => 'nullable|array',
            'digital_files.*.name' => 'required|string|max:255',
            'digital_files.*.path' => 'required|string|max:1000',
            'download_limit' => 'nullable|integer|min:0',
            'download_expiry' => 'nullable|integer|min:0',
            'status' => 'required|in:active,draft',
            'variants_enabled' => 'nullable|boolean',
            'variant_options' => 'nullable|json',
            'variant_data' => 'nullable|json',
            'attribute_options' => 'nullable|array',
            'attribute_options.*' => 'exists:attribute_options,id',
            'custom_fields_enabled' => 'nullable|boolean',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.label' => 'required|string|max:255',
            'custom_fields.*.type' => 'nullable|in:text,file',
            'custom_fields.*.file' => 'nullable|string|max:1000',
            'seo_title' => 'nullable|string|max:70',
            'seo_description' => 'nullable|string|max:160',
            'seo_slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]*$/', 'unique:products,slug'],
            'tiered_pricing_enabled' => 'nullable|boolean',
            'tier_prices' => 'nullable|array',
            'tier_prices.*.minimum_quantity' => 'required|integer|min:1',
            'tier_prices.*.maximum_quantity' => 'nullable|integer|min:1',
            'tier_prices.*.discount_type' => 'required|in:percentage,fixed_amount,fixed_price',
            'tier_prices.*.discount_value' => 'required|numeric|min:0',
            'tier_prices.*.status' => 'nullable|boolean',
        ];
    }
}
