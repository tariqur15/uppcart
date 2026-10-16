<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:attributes,code',
            'value_type' => 'required|in:text_only,color_only,image_only,mixed',
            'options' => 'nullable|array',
            'options.*.display_name' => 'required|string|max:255',
            'options.*.option_type' => 'required|in:text,color,image',
            'options.*.text_value' => 'required_if:options.*.option_type,text|string|max:255|nullable',
            'options.*.color_value' => 'required_if:options.*.option_type,color|string|max:20|nullable',
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'options.*.display_name.required' => 'Each option must have a display name.',
            'options.*.option_type.required' => 'Each option must have a type.',
            'options.*.text_value.required_if' => 'Text value is required when option type is Text.',
            'options.*.color_value.required_if' => 'Color value is required when option type is Color.',
        ];
    }
}
