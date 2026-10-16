<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $attributeId = $this->route('attribute')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:attributes,code,'.$attributeId,
            'value_type' => 'required|in:text_only,color_only,image_only,mixed',
        ];
    }
}
