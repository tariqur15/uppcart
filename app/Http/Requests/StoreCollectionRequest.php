<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:collections',
            'slug' => 'nullable|string|max:255|unique:collections,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'collection_type' => 'required|in:manual,automatic',
            'status' => 'required|in:active,inactive',
            'match_type' => 'nullable|required_if:collection_type,automatic|in:all,any',
            'sort_order' => 'nullable|integer|min:0',

            // Manual collection: selected products
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',

            // Automatic collection: conditions
            'conditions' => 'nullable|array',
            'conditions.*.field' => 'required|in:title,product_type,brand,category,tag,price,sale_price,stock,status,sku',
            'conditions.*.operator' => 'required|string',
            'conditions.*.value' => 'required|string',
        ];
    }
}
