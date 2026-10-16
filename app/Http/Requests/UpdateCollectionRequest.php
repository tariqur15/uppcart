<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $collection = $this->route('collection');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('collections')->ignore($collection)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('collections')->ignore($collection)],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'collection_type' => 'required|in:manual,automatic',
            'status' => 'required|in:active,inactive',
            'match_type' => 'nullable|required_if:collection_type,automatic|in:all,any',
            'sort_order' => 'nullable|integer|min:0',

            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',

            'conditions' => 'nullable|array',
            'conditions.*.field' => 'required|in:title,product_type,brand,category,tag,price,sale_price,stock,status,sku',
            'conditions.*.operator' => 'required|string',
            'conditions.*.value' => 'required|string',
        ];
    }
}
