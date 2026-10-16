<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id', function ($attribute, $value, $fail) {
                $product = Product::find($value);

                if ($product && $product->status !== 'active') {
                    $fail('This product is not available.');
                }

                if ($product && ! $product->isInStock()) {
                    $fail('This product is out of stock.');
                }
            }],
            'quantity' => ['integer', 'min:1', 'max:99'],
            'variant' => ['nullable', 'string'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.label' => ['required_with:custom_fields', 'string'],
            'custom_fields.*.value' => ['required_with:custom_fields'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'The selected product does not exist.',
        ];
    }
}
